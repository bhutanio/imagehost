<?php

namespace App\Http\Controllers\Image;

use App\Http\Controllers\Controller;
use App\Models\Albums;
use App\Models\Images;
use Bhutanio\Laravel\Services\Filer;
use Bhutanio\Laravel\Services\Imager;

class ViewImagesController extends Controller
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

    public function album($hash)
    {
        $album = Albums::where('hash', $hash)->with('images')->firstOrFail();
        $this->meta->setMeta(($album->album_title ? $album->album_title : 'Album '.$album->hash));
        return view('album', compact('album'));
    }

    public function image($hash)
    {
        $path = pathinfo($hash);
        if (!empty($path['extension']) && !empty($path['filename'])) {
            return $this->imageFile($path['filename']);
        }

        $image = Images::where('hash', $hash)->firstOrFail();
        $this->meta->setMeta(($image->image_title ? $image->image_title : 'Image '.$image->hash));
        return view('image', compact('image'));
    }

    private function imageFile($hash)
    {
        $image = Images::where('hash', $hash)->firstOrFail();

        $image_file = $this->imager->setImage($this->filer->type('images')->get($image->hash . '.' . $image->image_extension));

        return $image_file->response()
            ->setExpires(carbon()->addDays(7))
            ->header('Cache-Control', 'public,max-age=' . (3600 * 24 * 7) . ',s-maxage=' . (3600 * 24 * 7));
    }
}