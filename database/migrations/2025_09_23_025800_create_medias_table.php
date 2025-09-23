<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->string('file_name')->nullable();              // Tên file gốc
            $table->string('path')->nullable();                   // Đường dẫn trong bucket
            $table->string('url')->nullable();                    // URL công khai
            $table->string('mime_type')->nullable();  // Kiểu MIME
            $table->unsignedBigInteger('size')->nullable(); // Dung lượng file
            $table->integer('width')->nullable();     // Chiều rộng ảnh
            $table->integer('height')->nullable();    // Chiều cao ảnh
            $table->text('description')->nullable();  // Mô tả ảnh
            $table->json('tags')->nullable();         // Từ khóa gắn với ảnh
            $table->enum('visibility', ['public', 'private'])->default('public'); // Quyền truy cập
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Người upload
            $table->timestamp('uploaded_at')->nullable(); // Thời điểm upload
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};
