<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', static function (Blueprint $table): void {
            $table->id();
            $table->string('group', 120);
            $table->string('key', 150)->unique();
            $table->text('value')->nullable();
            $table->string('type', 50)->default('string');
            $table->string('label', 150)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_system')->default(false);
            $table->timestamps();

            $table->index(['group']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};