<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $files = Storage::disk('b2')->allFiles();
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        $imageFiles = array_filter($files, function ($file) use ($imageExtensions) {
            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $imageExtensions);
        });
        // $fileUrls = array_map( fn($file) => [
        //     'path' => $file,
        //     'url' => Storage::disk('b2')->url($file)
        // ] ,$imageFiles);

        foreach($imageFiles as $file){
            Media::create([
                'file_name' => $file,
                'path' => Storage::disk('b2')->url($file),
                'url' => Storage::disk('b2')->url($file),
                'mime_type' => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
            ]);
        }

    }
}
