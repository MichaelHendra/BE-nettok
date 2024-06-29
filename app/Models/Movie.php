<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Movie extends Model
{
    use HasFactory;

    protected static function boot(){
        static::creating(function ($model){
            if(! $model->getKey()){
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
    public function getIncrementing()
    {
        return false;
    }
    public function getKeyType()
    {
     return 'string'; 
    }
    protected $table = 'movie';
    protected $fillable = ['judul','gambar','tanggal_upload','tanggal_rilis','jenis_id','movie_link'];
}
