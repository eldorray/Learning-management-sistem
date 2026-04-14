<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nis')->nullable()->unique()->after('email'); // Nomor Induk Siswa
            $table->string('phone')->nullable()->after('nis');
            $table->text('address')->nullable()->after('phone');
            $table->string('gender')->nullable()->after('address'); // L / P
            $table->date('birth_date')->nullable()->after('gender');
            $table->string('guardian_name')->nullable()->after('birth_date');
            $table->string('class_group')->nullable()->after('guardian_name'); // Kelas / Rombel
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nis', 'phone', 'address', 'gender', 'birth_date', 'guardian_name', 'class_group']);
        });
    }
};
