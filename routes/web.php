<?php

use App\Http\Controllers\ChunkUploadController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::group([
    'controller' => UploadController::class,
], function () {
    Route::get('/', 'index')->name('home');
    Route::post('/handle-post', 'handlePost')->name('handlePost');
    Route::get('/get-all', 'getAll')->name('getAll');
    Route::get('/api/get-all', 'apiGetAll')->name('apiGetAll');
    Route::delete('/delete-file', 'deleteFile')->name('deleteFile');
    Route::get('/download', 'download')->name('download');
});
Route::post('/chunk-upload', ChunkUploadController::class)->name('chunkUpload');
