<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Movie extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Ensure there are no syntax errors or unexpected function calls here
            $model->id = (string) Str::uuid();
        });
    }

    protected $fillable = [
        'judul_movie', 'gambar', 'tanggal_upload', 'tanggal_rilis', 'jenis_id', 'movie_link', 'duration'
    ];
}
