<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('email_verified_at', '0000-00-00 00:00:00')
            ->update([
                'email_verified_at' => null,
            ]);

        if (!Schema::hasColumn('users', 'avatar_media_id')) {
            Schema::table('users', static function (Blueprint $table): void {
                $table->unsignedBigInteger('avatar_media_id')->nullable()->after('avatar_path');
            });
        }

        Schema::table('users', static function (Blueprint $table): void {
            $table->index('avatar_media_id', 'users_avatar_media_id_index');
        });

        DB::statement('
            ALTER TABLE `users`
            ADD CONSTRAINT `users_avatar_media_id_foreign`
            FOREIGN KEY (`avatar_media_id`)
            REFERENCES `media` (`id`)
            ON DELETE SET NULL
        ');
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'avatar_media_id')) {
            DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `users_avatar_media_id_foreign`');

            Schema::table('users', static function (Blueprint $table): void {
                $table->dropIndex('users_avatar_media_id_index');
                $table->dropColumn('avatar_media_id');
            });
        }
    }
};