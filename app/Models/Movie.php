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
            $model->id = (string) Str::uuid();
        });
    }

    protected $fillable = [
        'judul_movie', 'gambar', 'desk', 'tanggal_upload', 'tanggal_rilis', 'jenis_id', 'movie_link', 'duration'
    ];

    protected $appends = ['image_url', 'video_url'];

    private function generateUrl($path)
    {
        // Base URL for the Supabase storage bucket
        $baseUrl = env('SUPABASE_URL') . '/storage/v1/object/public/movie-assets/';

        // Return the full URL
        return $baseUrl . $path;
    }

    public function getImageUrlAttribute()
    {
        $path = $this->attributes['gambar'];
        return $this->generateUrl($path);
    }

    public function getVideoUrlAttribute()
    {
        $path = $this->attributes['movie_link'];
        return $this->generateUrl($path);
    }
}
