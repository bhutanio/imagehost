<?php

function meta()
{
    return app(App\Services\MetaDataService::class);
}

function carbon($time = null, $tz = null)
{
    return new \Carbon\Carbon($time, $tz);
}

function flash($message, $type = 'info')
{
    if ($type == 'error') {
        $type = 'danger';
    }

    if (!in_array($type, ['success', 'info', 'warning', 'danger'])) {
        $type = 'info';
    }

    app('session')->flash('flash_message', [
        'type'    => $type,
        'message' => $message,
    ]);
}

function asset_cdn($path)
{
    return asset($path);
}

function human_size($bytes, $decimals = 2)
{
    $bytes = (int)$bytes;
    $size = [' B', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB'];
    $factor = (int)floor((strlen($bytes) - 1) / 3);

    return number_format(($bytes / pow(1024, $factor)), $decimals) . @$size[$factor];
}

function computer_size($number, $size = null)
{
    if (!$size) {
        preg_match('/([0-9.]{0,9})\s?([bkmgtpezy]{1,2})/i', $number, $guess_size);
        if (isset($guess_size[1]) && isset($guess_size[2])) {
            $number = $guess_size[1];
            $size = $guess_size[2];
        }
    }

    $size = strtolower($size);
    $bytes = (float)$number;
    $factors = ['b' => 0, 'kb' => 1, 'mb' => 2, 'gb' => 3, 'tb' => 4, 'pb' => 5, 'eb' => 6, 'zb' => 7, 'yb' => 8];

    if (isset($factors[$size])) {
        return (float)number_format($bytes * pow(1024, $factors[$size]), 2, '.', '');
    }

    return $bytes;
}

function image_embed_codes($images, $type = null)
{
    $embed = '';
    if (!($images instanceof \Illuminate\Support\Collection)) {
        $images = [$images];
    }

    foreach ($images as $image) {
        $thumb_url = asset_cdn('t/' . $image->hash . '.' . $image->image_extension);
        $image_url = asset_cdn('i/' . $image->hash . '.' . $image->image_extension);
        if ($type == 'html') {
            $embed .= '<a href="' . $image_url . '"><img src="' . $thumb_url . '"' . "></a>\n";
        } elseif ($type == 'bbcode') {
            $embed .= '[img]' . $image_url . "[/img]\n";
        } else {
            $embed .= $image_url . "\n";
        }
    }

    $embed = rtrim($embed, "\n");

    return $embed;
}

function mime_to_extension($mime)
{
    try {
        $extension = \Hoa\Mime\Mime::getExtensionsFromMime($mime);
    } catch (\Exception $e) {
    }

    if (!empty($extension) && is_array($extension)) {
        if ($extension[0] == 'jpeg') {
            return 'jpg';
        }

        return $extension[0];
    }

    return null;
}

function extension_to_mime($extension)
{
    return \Hoa\Mime\Mime::getMimeFromExtension($extension);
}
