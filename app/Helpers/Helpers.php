<?php

function asset_version()
{
    if (!empty($version = env('ASSET_VERSION'))) {
        return $version;
    }

    try {
        $version = file_get_contents(base_path('version.txt'));
        $version = trim($version);
        putenv('ASSET_VERSION=' . $version);

        return $version;
    } catch (\Exception $e) {
    }

    return date('Ymd');
}

function asset_cdn($path)
{
    return url(env('SITE_CDN') . '/' . $path);
}


/**
 * Converts $bytes to a human readable size format.
 *
 * @param     $bytes
 * @param int $decimals
 *
 * @return string
 */
function human_size($bytes, $decimals = 2)
{
    $bytes = (int)$bytes;
    $size = [' B', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB'];
    $factor = (int)floor((strlen($bytes) - 1) / 3);

    return number_format(($bytes / pow(1024, $factor)), $decimals) . @$size[$factor];
}

/**
 * Converts number to computer size.
 *
 * @param $number
 * @param $size
 *
 * @return float
 */
function computer_size($number, $size)
{
    $bytes = (float)$number;
    $size = strtolower($size);

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
        $image_url = asset_cdn('i/' . $image->hash . '.' . $image->image_extension);
        if ($type == 'html') {
            $embed .= '<a href="' . $image_url . '">' . $image_url . "</a>\n";
        } elseif ($type == 'bbcode') {
            $embed .= '[img]' . $image_url . "[/img]\n";
        } else {
            $embed .= $image_url . "\n";
        }
    }

    $embed = rtrim($embed, "\n");

    return $embed;
}
