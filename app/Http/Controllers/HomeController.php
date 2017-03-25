<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        meta()->setMeta('ImageZ', 'ImageZ - Free and Secure Image Hosting & Photo Sharing');

        return view('home');
    }
}
