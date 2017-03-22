<?php

namespace App\Http\Controllers;

use App\Services\MetaDataService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
