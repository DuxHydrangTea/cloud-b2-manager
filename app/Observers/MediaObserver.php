<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class MediaObserver
{
    //
    public function created(){
        $this->clearMediaCache();
    }

    public function deleted(){
        $this->clearMediaCache();
    }

    public function updated(){
        $this->clearMediaCache();
    }

    protected function clearMediaCache()
    {
        $perPage = 30;
        $pageCount = Cache::get('media_api_page_count', 1);

        for ($page = 1; $page <= $pageCount; $page++) {
            Cache::forget("media_page_{$page}_per_{$perPage}");
        }
    }
}
