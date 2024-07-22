<?php

namespace App\Http\Controllers;

use App\Helpers\SupabaseHelper;
use App\Http\Resources\ApihResource;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MovieController extends Controller
{

    protected $supabaseHelper;

    public function __construct(){
        $this->supabaseHelper = new SupabaseHelper();
    }

    public function index() {
        $data = Movie::join('jenis_movie', 'jenis_movie.jenis_id', '=', 'movies.jenis_id')->get();
        return response()->json($data);
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_movie' => 'required',
            'gambar' => 'required|image|mimes:png,jpeg,jpg,gif',
            'desk' => 'required',
            'tanggal_rilis' => 'required',
            'jenis_id' => 'required',
            'movie_link' => 'required|mimes:mp4,mov,ogg,qt',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $imagePath = null;
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->getPathname();
            $this->supabaseHelper->uploadFile($imagePath, 'images/' . $imageName);
            $imagePath = 'images/' . $imageName;
        }

        $videoPath = null;
        if ($request->hasFile('movie_link')) {
            $video = $request->file('movie_link');
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $videoPath = $video->getPathname();
            $this->supabaseHelper->uploadFile($videoPath, 'videos/' . $videoName);
            $videoPath = 'videos/' . $videoName;
        }

        $saatIni = Carbon::now()->toDateString();
        $data = Movie::create([
            'judul_movie' => $request->judul_movie,
            'gambar' => $imagePath,
            'desk' => $request->desk,
            'tanggal_upload' => $saatIni,
            'tanggal_rilis' => $request->tanggal_rilis,
            'jenis_id' => $request->jenis_id,
            'movie_link' => $videoPath,
            'duration' => 0,
        ]);

        return new ApihResource(true, "Data Berhasil Ditambah", $data);
    }


    public function show($id)
    {
        // // try {
        //     $movie = Movie::find($id);
        $movie = Movie::join('jenis_movie', 'jenis_movie.jenis_id', '=', 'movies.jenis_id')
        ->where('movies.id', '=', $id)
        ->select('movies.*','jenis_movie.jenis_id as jenis_idih', 'jenis_movie.jenis as jenis')
        ->first();

            return response()->json($movie);
        // } catch (\Exception $e) {
        //     // Handle exception
        //     return response()->json(['error' => 'Movie not found'], 404);
        // }
    }


    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'judul_movie' => 'required|string|max:255',
        'tanggal_rilis' => 'required|date',
        'desk' => 'required',
        'jenis_id' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $movie = Movie::findOrFail($id);

    // Handle image upload
    if ($request->hasFile('gambar')) {
        if ($movie->gambar) {
            // Delete the existing image from Supabase
            $this->supabaseHelper->deleteFile('images/' . $movie->gambar);
        }
        $image = $request->file('gambar');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->getPathname();
        $this->supabaseHelper->uploadFile($imagePath, 'images/' . $imageName);
        $movie->gambar = $imageName;
    }

    // Handle video upload
    if ($request->hasFile('movie_link')) {
        if ($movie->movie_link) {
            // Delete the existing video from Supabase
            $this->supabaseHelper->deleteFile('videos/' . $movie->movie_link);
        }
        $video = $request->file('movie_link');
        $videoName = time() . '.' . $video->getClientOriginalExtension();
        $videoPath = $video->getPathname();
        $this->supabaseHelper->uploadFile($videoPath, 'videos/' . $videoName);
        $movie->movie_link = $videoName;
    }

    $movie->judul_movie = $request->input('judul_movie');
    $movie->desk = $request->input('desk');
    $movie->tanggal_rilis = $request->input('tanggal_rilis');
    $movie->jenis_id = $request->input('jenis_id');
    $movie->save();

    return response()->json(['success' => 'Movie updated successfully']);
}



public function destroy($id)
{
    try {
        $movie = Movie::findOrFail($id);

        // Delete the video file from Supabase
        if ($movie->movie_link) {
            $this->supabaseHelper->deleteFile('videos/' . $movie->movie_link);
        }

        // Delete the image file from Supabase
        if ($movie->gambar) {
            $this->supabaseHelper->deleteFile('images/' . $movie->gambar);
        }

        // Delete the movie record from the database
        $movie->delete();

        return response()->json(['success' => 'Movie deleted successfully']);
    } catch (\Exception $e) {
        // Handle exception
        return response()->json(['error' => 'Movie not found'], 404);
    }
}

public function genre($id)  {
    $data = Movie::join('jenis_movie', 'movies.jenis_id','jenis_movie.jenis_id')
    ->select('movies.*','jenis_movie.jenis as jenis')
    ->where('movies.jenis_id',$id)->get();
    return response()->json($data);
}

public function barat() {
    $data = Movie::where('jenis_id', 1)->get();

    return response()->json($data);

}

public function search($key) {
    $data = Movie::whereRaw('LOWER(judul_movie) LIKE ?', ['%' . strtolower($key) . '%'])->get();
    return response()->json($data);
}

}
