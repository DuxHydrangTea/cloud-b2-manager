<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    //
    public function index(){
        return view('index');
    }

    public function handlePost(Request $request){
        $file = $request->file('file');
        $fileName = time() . $file->getClientOriginalName();
        $path = $file->storeAs('key2', $fileName, 'b2');

        return response()->json([
            'success' => true,
            'path' => $path,
        ]);
    }

    public function getAll(){
        return view('all-files');
    }

    public function apiGetAll(Request $request){
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $cacheKey = "media_page_{$page}_per_{$perPage}";

        $images = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($perPage) {
            return Media::orderBy('created_at', 'desc')->paginate($perPage);
        });

        Cache::remember('media_api_page_count', now()->addMinutes(5), fn() => $images->lastPage());

        return response()->json($images);
    }

    public function deleteFile(Request $request){
        $media = Media::find($request->id);

        if(!$media){
             return response()->json(false);
        }

        $file_name = $media->file_name;
        $media->delete();
        $delete = Storage::disk('b2')->delete($file_name);

        return response()->json($delete);
    }

    public function download(Request $request){
        $path =  $request->path;

        return Storage::disk('b2')->download($path);
    }


}
