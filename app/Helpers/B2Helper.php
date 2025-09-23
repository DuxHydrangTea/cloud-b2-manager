<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class B2Helper {
    private $b2Storage;

    public static $imageMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'image/bmp',
        'image/tiff',
        'image/x-icon',
        'image/heic',
        'image/avif',
    ];

    public static $videoMimeTypes = [
        'video/mp4',
        'video/webm',
        'video/ogg',
        'video/quicktime',     // MOV
        'video/x-msvideo',     // AVI
        'video/x-matroska',    // MKV
        'video/x-flv',
    ];

    public static $documentMimeTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',       // XLSX
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation', // PPTX
        'text/plain',
        'text/csv',
        'application/zip',
        'application/x-rar-compressed',
    ];

    public static $soundMimeTypes = [
        'audio/mpeg',          // MP3
        'audio/ogg',
        'audio/wav',
        'audio/webm',
        'audio/x-ms-wma',
        'audio/aac',
        'audio/flac',
    ];

    public static $xmlMimeTypes = [
        'application/json',
        'application/xml',
        'text/xml',
    ];

    public static $imageFolder = 'images';

    public static $videoFolder = 'videos';

    public static $documentFolder = 'documents';

    public static $soundFolder = 'sounds';

    public static $xmlFolder = 'xmls';

    public function __construct()
    {
        $this->b2Storage = Storage::disk('b2');
    }

    public function uploadImage($file, $path = ''){
        if (in_array($file->getMimeType(), self::$imageMimeTypes)) {
            $path = $this->b2Storage->putFile( self::$imageFolder . $path, $file);

            return $path;
        }

        return null;
    }


}
