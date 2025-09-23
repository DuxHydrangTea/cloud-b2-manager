<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class ChunkUploadController extends Controller
{
    //
    public function __invoke(Request $request)
    {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if ($receiver->isUploaded()) {
            $save = $receiver->receive();

            if ($save->isFinished()) {
                $file = $save->getFile();
                $storeFileName = time() . $file->getClientOriginalName();
                $path = $file->storePubliclyAs('key2', $storeFileName, 'b2');
                $fileName = $file->getClientOriginalName();
                $file->move(storage_path('app/uploads'), $fileName);

                Media::create([
                    'file_name' => $path,
                    'path' => Storage::disk('b2')->url($path),
                    'url' => Storage::disk('b2')->url($path),
                    'mime_type' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                ]);
                return response()->json(['success' => true]);
            } else {
                return response()->json(['chunk_received' => true]);
            }
        }

        return response()->json(['error' => 'Upload failed'], 400);
    }
}
