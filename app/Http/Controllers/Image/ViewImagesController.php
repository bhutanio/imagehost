<?php

namespace App\Http\Controllers\Image;

use App\Http\Controllers\Controller;
use App\Models\Albums;
use App\Models\Images;
use App\Services\Filer;
use App\Services\Imager;

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
        $album = Albums::where('hash', $hash)->with(['images'])->firstOrFail();
        $images = Images::where('album_id', $album->id)->paginate(20);
        $this->meta->setMeta(($album->album_title ? $album->album_title : 'Album ' . $album->hash));

        return view('album', compact('album', 'images'));
    }

    public function image($hash)
    {
        $extension = str_contains($hash, '.');
        if ($extension) {
            return $this->imageFile($hash);
        }

        $image = Images::where('hash', $hash)->firstOrFail();
        $this->meta->setMeta(($image->image_title ? $image->image_title : 'Image ' . $image->hash));

        return view('image', compact('image'));
    }

    public function thumbnail($hash)
    {
        if ($hash) {
            return $this->imageFile($hash, true);
        }

        abort(404, 'Image Not Found!');
    }

    private function imageFile($filename, $thumb = false)
    {
//        $hash = explode('.', $hash)[0];
//        $image = Images::where('hash', $hash)->firstOrFail();

        $file_content = $this->filer->type('images')->get($filename);
        if (empty($file_content)) {
            abort(404, 'Image Not Found');
        }

        $image_file = $this->imager->setImage($file_content);

        if ($thumb) {
            $image_file->fit(150, 100);
        }

        if (str_contains($filename, '.gif') && !$thumb) {
            return response($file_content, 200,
                [
                    'Content-Type'  => 'image/gif',
                    'Cache-Control' => 'public,max-age=' . (3600 * 24 * 7) . ',s-maxage=' . (3600 * 24 * 7),
                ])->setExpires(carbon()->addDays(7));
        }

        return $image_file->response()
            ->setExpires(carbon()->addDays(7))
            ->header('Cache-Control', 'public,max-age=' . (3600 * 24 * 7) . ',s-maxage=' . (3600 * 24 * 7));
    }
}