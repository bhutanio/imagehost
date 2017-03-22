<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Albums extends Model
{
    protected $table = 'albums';

    protected $fillable = [
        'hash',
        'album_title',
        'album_description',
        'adult',
        'private',
        'expire',
        'created_by'
    ];

    public function images()
    {
        return $this->hasMany(Images::class, 'album_id', 'id');
    }
}
