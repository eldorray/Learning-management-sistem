<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surahs', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('nomor')->unique(); // 1-114
            $table->string('nama_arab');
            $table->string('nama_latin');
            $table->string('nama_indonesia');
            $table->unsignedSmallInteger('jumlah_ayat');
            $table->unsignedTinyInteger('juz'); // juz pertama surah ini (1-30)
            $table->enum('tempat_turun', ['makkiyah', 'madaniyah']);
            $table->timestamps();
        });

        Schema::create('tahfidz_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instruktur_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_halaqoh');
            $table->string('tingkat_kelas')->nullable(); // e.g. "Kelas 7A"
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tahfidz_group_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahfidz_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->date('joined_at')->nullable();
            $table->unique(['tahfidz_group_id', 'student_id']);
            $table->timestamps();
        });

        Schema::create('tahfidz_targets', function (Blueprint $table) {
            $table->id();
            $table->string('tingkat_kelas'); // e.g. "Kelas 7", "Kelas 8"
            $table->string('semester')->default('1'); // "1" or "2"
            $table->foreignId('surah_mulai_id')->constrained('surahs');
            $table->unsignedSmallInteger('ayat_mulai')->default(1);
            $table->foreignId('surah_selesai_id')->constrained('surahs');
            $table->unsignedSmallInteger('ayat_selesai');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('tahfidz_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('instruktur_id')->constrained('users');
            $table->foreignId('surah_id')->constrained('surahs');
            $table->unsignedSmallInteger('ayat_mulai');
            $table->unsignedSmallInteger('ayat_selesai');
            $table->enum('jenis_setoran', ['ziyadah', 'murojaah']);
            $table->unsignedTinyInteger('score_kelancaran')->default(0); // 0-100
            $table->unsignedTinyInteger('score_tajwid')->default(0);     // 0-100
            $table->unsignedTinyInteger('score_makhorijul_huruf')->default(0); // 0-100
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'approved', 'revision'])->default('approved');
            $table->date('tanggal_setoran');
            $table->timestamps();

            $table->index(['student_id', 'tanggal_setoran']);
            $table->index(['instruktur_id', 'tanggal_setoran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahfidz_records');
        Schema::dropIfExists('tahfidz_targets');
        Schema::dropIfExists('tahfidz_group_students');
        Schema::dropIfExists('tahfidz_groups');
        Schema::dropIfExists('surahs');
    }
};
