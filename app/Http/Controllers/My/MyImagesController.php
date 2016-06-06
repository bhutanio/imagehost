<?php

namespace App\Http\Controllers\My;

use App\Http\Controllers\Controller;
use App\Models\Albums;
use App\Models\Images;
use Auth;
use Bhutanio\Laravel\Services\Filer;
use Bhutanio\Laravel\Services\Imager;

class MyImagesController extends Controller
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
        $this->meta->setMeta('My Uploads');

        $data = [
            'url'    => 'my',
            'albums' => Albums::where('created_by', Auth::id())->count(),
            'images' => Images::where('created_by', Auth::id())->whereNull('album_id')->count(),
        ];

        return view('my.index', $data);
    }

    public function albums()
    {
        $this->meta->setMeta('My Albums');

        $data = [
            'url'    => 'my',
            'albums' => Albums::with(['images'])->where('created_by', Auth::id())->latest()->paginate(10),
        ];

        return view('my.albums', $data);
    }

    public function images()
    {
        $this->meta->setMeta('My Images');

        $data = [
            'url'    => 'my',
            'images' => Images::where('created_by', Auth::id())->whereNull('album_id')->latest()->paginate(20),
        ];

        return view('my.images', $data);
    }
}