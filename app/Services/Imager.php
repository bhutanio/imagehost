<?php

namespace App\Services;

use Intervention\Image\Facades\Image;

class Imager extends Filer
{
    /**
     * @var \Intervention\Image\Image
     */
    public $image;

    protected $cached = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function getImage($format = null, $quality = 90)
    {
        return $this->image->stream($format, $quality);
    }

    public function setImage($file, $type = null)
    {
        $this->type($type);
        $this->image = Image::make($file);

        return $this;
    }

    public function getColor()
    {
        return $this->image->resize(1, 1)->pickColor(0, 0, 'hex');
    }

    public function getInfo()
    {
        return [
            'mime'      => $this->image->mime(),
            'width'     => $this->image->width(),
            'height'    => $this->image->height(),
            'extension' => $this->image->extension,
            'filename'  => $this->image->filename,
            'filesize'  => $this->image->filesize(),
        ];
    }

    public function crop($width, $height)
    {
        $this->image->crop($width, $height);

        return $this;
    }

    public function resize($width, $height, $aspect_ratio = true, $upscale = true)
    {
        $this->image->resize($width, $height, function ($constraint) use ($aspect_ratio, $upscale) {
            if ($aspect_ratio) {
                $constraint->aspectRatio();
            }
            if ($upscale) {
                $constraint->upsize();
            }
        });

        return $this;
    }

    /**
     * @param $width
     * @param null $height
     * @param string $position top-left|top|top-right|left|center|right|bottom-left|bottom|bottom-right
     *
     * @return $this
     */
    public function fit($width, $height = null, $position = 'center')
    {
        $this->image->fit($width, $height, null, $position);

        return $this;
    }

    public function response($format = null, $quality = 90)
    {
        return $this->image->response($format, $quality);
    }

    public function __call($method, $parameters)
    {
        $this->image = call_user_func_array([$this->image, $method], $parameters);

        return $this;
    }
}
