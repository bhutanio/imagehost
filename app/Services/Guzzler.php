<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Cache;

class Guzzler
{
    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * @var int
     */
    protected $status_code;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $header;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var bool|int
     */
    protected $cached = false;

    /**
     * @var string
     */
    protected $cache_id;

    /**
     * @var string
     */
    protected $cache_prefix = 'guzzler:';

    /**
     * @var array
     */
    protected $query = [];

    /**
     * @var GuzzleClient
     */
    private $client;

    public function __construct($config = [])
    {
        $this->client = new GuzzleClient($config);
    }

    /**
     * Set URL for HTTP requests.
     *
     * @param string $url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set basic HTTP authentication.
     *
     * @param $username string
     * @param $password string
     *
     * @return self
     */
    public function setAuth($username, $password)
    {
        $this->query['auth'] = [$username, $password];

        return $this;
    }

    /**
     * Return response headers.
     *
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Return response body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Cache response keyed by url.
     *
     * @param $duration
     * @param $cache_id
     *
     * @return self
     */
    public function cache($duration, $cache_id = null)
    {
        $this->cached = $duration;
        if ($cache_id) {
            $this->cache_id = $this->cache_prefix . $cache_id;
        }

        return $this;
    }

    /**
     * Perform a request.
     *
     * @param string $method
     * @param array $options
     *
     * @return self
     */
    public function request($method, array $options = [])
    {
        if ($method == 'GET' && $this->getCache()) {
            return $this;
        }

        $this->response = $this->client->request($method, $this->url, $this->mergeOptions($options));
        $this->status_code = $this->response->getStatusCode();
        $this->header = $this->response->getHeaders();
        $this->body = (string)$this->response->getBody();

        if ($method == 'GET' && $this->cached) {
            $this->putCache();
        }

        return $this;
    }

    /**
     * Perform get request.
     *
     * @param array $options
     *
     * @return self
     */
    public function get(array $options = [])
    {
        return $this->request('GET', $options);
    }

    /**
     * Perform post request.
     *
     * @param array $options
     *
     * @return self
     */
    public function post(array $options = [])
    {
        return $this->request('POST', $options);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->body && $this->isJson($this->body)) {
            return json_decode($this->body, true);
        }

        return;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        if ($this->body && $this->isJson($this->body)) {
            return json_encode(json_decode($this->body));
        }

        return;
    }

    /**
     * Attach query to the request.
     *
     * @param array $query
     *
     * @return self
     */
    public function withQuery(array $query)
    {
        if (!empty($this->query['query'])) {
            $this->query['query'] = array_merge($this->query['query'], $query);

            return $this;
        }

        $this->query = array_merge($this->query, ['query' => $query]);

        return $this;
    }

    /**
     * Attach data stream to the request.
     *
     * @param $body
     *
     * @return self
     */
    public function withBody($body)
    {
        $this->query = array_merge($this->query, ['body' => $body]);

        return $this;
    }

    /**
     * Attach form data to the request.
     *
     * @param array $form ['field_name'=>'value', ...]
     *
     * @return self
     */
    public function withForm(array $form)
    {
        if (!empty($this->query['form_params'])) {
            $this->query['form_params'] = array_merge($this->query['form_params'], $form);

            return $this;
        }

        $this->query = array_merge($this->query, ['form_params' => $form]);

        return $this;
    }

    /**
     * Attach multipart form data to the request.
     *
     * @param array $form [[name, contents, filename], ...]
     *
     * @return self
     */
    public function withMultiForm(array $form)
    {
        if (!empty($this->query['multipart'])) {
            $this->query['multipart'] = array_merge($this->query['multipart'], $form);

            return $this;
        }

        $this->query = array_merge($this->query, ['multipart' => $form]);

        return $this;
    }

    /**
     * Attach file as multipart form data to the request.
     *
     * @param array $file [name, contents, filename]
     *
     * @return self
     */
    public function withFile(array $file)
    {
        return $this->withMultiForm([$file]);
    }

    public function getStatusCode()
    {
        return $this->status_code;
    }

    public function unCache()
    {
        if ($this->cached) {
            $this->getCacheId();
            Cache::forget($this->cache_id);
        }
    }

    private function putCache()
    {
        if ($this->cached && $this->header && $this->body) {
            $this->getCacheId();

            $data = [
                'url'         => $this->url,
                'query'       => $this->query,
                'status_code' => $this->status_code,
                'header'      => $this->header,
                'body'        => $this->body,
            ];
            Cache::put($this->cache_id, $data, $this->cached);
        }
    }

    private function getCache()
    {
        if ($this->cached) {
            $this->getCacheId();

            $data = Cache::get($this->cache_id);
            if ($data && is_array($data)) {
                $this->status_code = $data['status_code'];
                $this->query = $data['query'];
                $this->header = $data['header'];
                $this->body = $data['body'];

                return true;
            }
        }

        return false;
    }

    private function isJson($string)
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }

    private function mergeOptions($options)
    {
        if (!empty($this->query)) {
            if (!empty($this->query['multipart']) && !empty($this->query['form_params'])) {
                $form_params = $this->query['form_params'];
                foreach ($form_params as $key => $value) {
                    $form_param = [
                        'name'     => $key,
                        'contents' => $value,
                    ];
                    array_push($this->query['multipart'], $form_param);
                }
                unset($this->query['form_params']);
            }

            return array_merge_recursive($options, $this->query);
        }

        return $options;
    }

    private function getCacheId()
    {
        if (empty($this->cache_id)) {
            $query_string = '';
            if (!empty($this->query['query'])) {
                $query_string = '?' . http_build_query($this->query['query']);
            }
            $this->cache_id = $this->cache_prefix . md5($this->url . $query_string);
        }
    }
}
