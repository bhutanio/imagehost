<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Albums;
use App\Models\Images;
use App\Services\Filer;
use App\Services\Imager;

class AdminImagesController extends Controller
{
    /**
     * @var Filer
     */
    private $filer;

    /**
     * @var Imager
     */
    private $imager;

    public function __construct()
    {
        parent::__construct();
        $this->filer = app(Filer::class);
        $this->imager = app(Imager::class);
    }

    public function index()
    {
        $this->meta->setMeta('Uploads');

        $data = [
            'url'    => 'admin',
            'albums' => Albums::count(),
            'images' => Images::whereNull('album_id')->count(),
        ];

        return view('my.index', $data);
    }

    public function albums()
    {
        $this->meta->setMeta('Albums');

        $data = [
            'url'    => 'admin',
            'albums' => Albums::with(['images'])->latest()->paginate(10),
        ];

        return view('my.albums', $data);
    }

    public function images()
    {
        $this->meta->setMeta('Images');

        $data = [
            'url'    => 'admin',
            'images' => Images::whereNull('album_id')->latest()->paginate(20),
        ];

        return view('my.images', $data);
    }
}