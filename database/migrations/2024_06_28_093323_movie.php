<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movie', function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->string('judul_movie');
            $table->string('gambar');
            $table->date('tanggal_upload');
            $table->date('tanggal_rilis');
            $table->integer('jenis_id');
            $table->string('movie_link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie');
    }
};
