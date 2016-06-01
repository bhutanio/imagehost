<?php

namespace App\Http\Controllers;

use Bhutanio\Laravel\Services\MetaDataService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    /**
     * @var MetaDataService
     */
    protected $meta;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct()
    {
        $this->meta = app(MetaDataService::class);
        $this->request = app('request');
    }
}
