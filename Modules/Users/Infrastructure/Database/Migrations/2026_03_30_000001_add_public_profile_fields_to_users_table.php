<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $table): void {
            $table->string('slug', 180)->nullable()->after('name');
            $table->text('bio')->nullable()->after('password');
            $table->string('avatar_path', 255)->nullable()->after('bio');
        });

        DB::table('users')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->chunkById(100, static function ($users): void {
                foreach ($users as $user) {
                    $baseSlug = Str::slug((string) $user->name);
                    $baseSlug = $baseSlug !== '' ? $baseSlug : 'user-' . $user->id;
                    $slug = $baseSlug;
                    $suffix = 1;

                    while (
                        DB::table('users')
                            ->where('slug', $slug)
                            ->where('id', '!=', $user->id)
                            ->exists()
                    ) {
                        $slug = $baseSlug . '-' . $suffix;
                        $suffix++;
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['slug' => $slug]);
                }
            });

        Schema::table('users', static function (Blueprint $table): void {
            $table->unique('slug');
            $table->index('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropIndex(['email_verified_at']);
            $table->dropColumn([
                'slug',
                'bio',
                'avatar_path',
            ]);
        });
    }
};