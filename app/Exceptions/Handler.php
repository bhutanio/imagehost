<?php

namespace App\Exceptions;

use ErrorException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Validation\ValidationException;
use Illuminate\Session\TokenMismatchException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        TokenMismatchException::class,
    ];

    /**
     * Handler constructor.
     */
    public function __construct(LoggerInterface $log)
    {
        parent::__construct($log);
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        meta()->setMeta('Error');

        if ($e instanceof TokenMismatchException) {
            return $this->handleTokenMismatch($request);
        }

        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException('Database returned no result!', $e);
        }

        if ($request->ajax() || $request->wantsJson()) {
            if (method_exists($e, 'getStatusCode')) {
                return response()->json($e->getMessage(), $e->getStatusCode());
            }
            if ($e instanceof AuthorizationException) {
                return response()->json('Unauthorized Action', 403);
            }

            return response()->json('Error', 500);
        }

        if (($e instanceof ErrorException) && app()->environment() != 'local') {
            meta()->setMeta('Error 500');

            return response()->view('errors.500', [], 500);
        }

        if ($e instanceof AuthorizationException) {
            meta()->setMeta('Error 403', 'Error 403: Unauthorized Action');
        }

        if (method_exists($e, 'getStatusCode')) {
            $error_title = 'Error';
            switch ($e->getStatusCode()) {
                case 403:
                    $error_title .= ' 403: Access Denied';
                    break;
                case 404:
                    $error_title .= ' 404: Page not found';
                    break;
            }
            meta()->setMeta('Error ' . $e->getStatusCode(), $error_title);
        }

        return parent::render($request, $e);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    private function handleTokenMismatch($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json('Your session has expired. Please try again.', 401);
        }

        flash()->warning('Your session has expired. Please try again.');

        return redirect()->back()->withInput($request->except('_token'));
    }
}
