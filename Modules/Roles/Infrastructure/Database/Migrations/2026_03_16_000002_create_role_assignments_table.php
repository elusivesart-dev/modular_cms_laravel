<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_assignments', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('subject_type', 255);
            $table->string('subject_id', 64);
            $table->timestamps();

            $table->unique(['role_id', 'subject_type', 'subject_id'], 'roles_assignments_unique');
            $table->index(['subject_type', 'subject_id'], 'roles_assignments_subject_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_assignments');
    }
};