<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApihResource;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    
    public function index() {
        $data = Movie::all();
        return new ApihResource(true,"Data Ada",$data);
    }
    
    
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_movie' => 'required',
            'gambar' => 'required|image|mimes:png,jpeg,jpg,gif',
            'tanggal_rilis' => 'required',
            'jenis_id' => 'required',
            'movie_link' => 'required|mimes:mp4,mov,ogg,qt',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
        }
    
        if ($request->hasFile('movie_link')) {
            $video = $request->file('movie_link');
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('videos'), $videoName);
        }
        
       $data = Movie::create([
            'judul_movie' => $request->judul_movie,
            'gambar' => $imageName,
            'tanggal_upload' => $request->tanggal_upload,
            'tanggal_rilis' => $request->tanggal_rilis,
            'jenis_id' => $request->jenis_id,
            'movie_link' => $videoName,
            'duration' => 0
        ]);
    
        return new ApihResource(true,"Data Berhasi Ditambah", $data);
    }

    
    public function show($id)
    {
        try {
            $movie = Movie::findOrFail($id);

            return response()->json([
                'judul_movie' => $movie->judul_movie,
                'gambar' => asset('images/' . $movie->gambar),
                'tanggal_upload' => $movie->tanggal_upload,
                'tanggal_rilis' => $movie->tanggal_rilis,
                'jenis_id' => $movie->jenis_id,
                'movie_link' => asset('videos/' . $movie->movie_link),
                'duration' => $movie->duration,
            ]);
        } catch (\Exception $e) {
            // Handle exception
            return response()->json(['error' => 'Movie not found'], 404);
        }
    }

    
    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'judul_movie' => 'required',
        'tanggal_rilis' => 'required',
        'jenis_id' => 'required',
        'movie_link' => 'required|mimes:mp4,mov,ogg,qt',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $movie = Movie::findOrFail($id);
    
    $oldGambar = $movie->gambar;
    $oldVideo = $movie->movie_link;

   
    if ($request->hasFile('gambar')) {
        if($oldGambar){
            $gambarPath = public_path('images/').$oldGambar;
            if(file_exists($gambarPath)){
                unlink($gambarPath);
            }
        }
        $image = $request->file('gambar');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
        $movie->gambar = $imageName;
    }

    if ($request->hasFile('movie_link')) {
        if($oldVideo){
            $videoPath = public_path('videos/').$oldVideo;
            if(file_exists($videoPath)){
                unlink($videoPath);
            }
        }
        $video = $request->file('movie_link');
        $videoName = time() . '.' . $video->getClientOriginalExtension();
        $video->move(public_path('videos'), $videoName);
        $movie->movie_link = $videoName;
    }

    $movie->judul_movie = $request->judul_movie;
    $movie->tanggal_rilis = $request->tanggal_rilis;
    $movie->jenis_id = $request->jenis_id;
    $movie->save();

    return response()->json(['success' => 'Movie updated successfully']);
}

    
public function destroy($id)
{
    try {
        $movie = Movie::findOrFail($id);

        // Delete the video file
        if ($movie->movie_link) {
            $videoPath = public_path('videos/') . $movie->movie_link;
            if (file_exists($videoPath)) {
                unlink($videoPath); // Delete the file
            }
        }

        // Delete the image file
        if ($movie->gambar) {
            $imagePath = public_path('images/') . $movie->gambar;
            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the file
            }
        }

        // Delete the movie record from the database
        $movie->delete();

        return response()->json(['success' => 'Movie deleted successfully']);
    } catch (\Exception $e) {
        // Handle exception
        return response()->json(['error' => 'Movie not found'], 404);
    }
}
}
