<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $table = 'images';

    protected $fillable = [
        'album_id',
        'hash',
        'file_hash',
        'image_title',
        'image_description',
        'image_extension',
        'image_width',
        'image_height',
        'created_by'
    ];
}
