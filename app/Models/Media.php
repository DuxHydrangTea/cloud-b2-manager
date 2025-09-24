<?php

namespace App\Models;

use App\Helpers\B2Helper;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    //
    protected $table = 'medias';

    protected $guarded = [];

    public function scopeImage(){
        $mimeTypes = [
            'png',
            'jpg',
            'jpeg',
            'webp'
        ];
        return $this->whereIn('mime_type', $mimeTypes);
    }
}
