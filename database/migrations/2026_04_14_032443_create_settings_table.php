<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        DB::table('settings')->insert([
            ['key' => 'app_name', 'value' => 'LMS Arrahmah', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_tagline', 'value' => 'Platform Pembelajaran Digital', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_logo', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_favicon', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
