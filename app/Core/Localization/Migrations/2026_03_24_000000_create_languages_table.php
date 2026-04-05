<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name', 120);
            $table->string('native_name', 120);
            $table->string('direction', 3)->default('ltr');
            $table->string('version', 32)->nullable();
            $table->string('installed_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['is_system']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};