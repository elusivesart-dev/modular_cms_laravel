<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', static function (Blueprint $table): void {
            $table->id();
            $table->string('disk', 50);
            $table->string('directory', 255);
            $table->string('path', 255)->unique();
            $table->string('filename', 255);
            $table->string('original_name', 255);
            $table->string('mime_type', 150);
            $table->string('extension', 20)->default('');
            $table->unsignedBigInteger('size')->default(0);
            $table->string('visibility', 20)->default('public');
            $table->string('title', 255)->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['disk']);
            $table->index(['mime_type']);
            $table->index(['uploaded_by']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};