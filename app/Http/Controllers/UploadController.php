<?php

namespace App\Http\Controllers;

use App\Jobs\UploadToB2Queue;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpZip\ZipFile;
use ZipArchive;

class UploadController extends Controller
{
    //
    public function index(){
        return view('index');
    }

    public function handlePost(Request $request){
        $file = $request->file('file');
        $fileName = time() . $file->getClientOriginalName();
        $path = $file->storeAs('', $fileName, 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
        ]);
    }

    public function getAll(){
        $files = Storage::disk('cl')->allFiles();
        dd($files);
        return view('all-files');
    }

    public function apiGetAll(Request $request){
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $cacheKey = "media_page_{$page}_per_{$perPage}";

        $images = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($perPage) {
            return Media::image()->orderBy('created_at', 'desc')->paginate($perPage);
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

    public function res(){
        $path = 'resource.zip';
        $zipAbsolutePath = Storage::disk('public')->path($path);
        [$extractPath, $folderName, $folderPath]= $this->getNewFolderName($path);
        $zip = new ZipArchive;
        if ($zip->open($zipAbsolutePath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
            // $this->uploadFolder($folderName);
            // UploadToB2Queue::dispatch($folderName);
            $files = File::allFiles(Storage::disk('public')->path($folderName));

            foreach ($files as $file) {
                $relativePath = str_replace(
                    storage_path('app/public'),
                    '',
                    $file->getPathname()
                );

                Storage::disk('cl')->put(
                    $relativePath,
                    file_get_contents($file->getRealPath())
                );
            }
            return true;
        } else {
            return false;
        }
    }

    private function getNewFolderName($path){
        $folderName = pathinfo($path, PATHINFO_FILENAME);
        $extractPath = Storage::disk('public')->path($folderName);

        if (!Storage::disk('public')->exists($folderName)) {
            Storage::disk('public')->makeDirectory($folderName);
        }

        return [$extractPath, $folderName, Storage::disk('public')->path($folderName)];

    }

    private function uploadFolder($folderName, $prefix = ''){
        $files = File::allFiles(Storage::disk('public')->path($folderName));

        foreach ($files as $file) {
            $relativePath = str_replace(
                storage_path('app/public'),
                '',
                $file->getPathname()
            );

            Storage::disk('b2')->put(
                $prefix . $relativePath,
                file_get_contents($file->getRealPath())
            );
        }
    }

}
