<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UploadToB2Queue implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $localPath,
        // public string $b2Path,
    )
    {
        $this->localPath = $localPath;
        // $this->b2Path = $b2Path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $files = File::allFiles(Storage::disk('public')->path($this->localPath));

        foreach ($files as $file) {
            $relativePath = str_replace(
                storage_path('app/public'),
                '',
                $file->getPathname()
            );

            Storage::disk('b2')->put(
                $relativePath,
                file_get_contents($file->getRealPath())
            );
        }
    }
}
