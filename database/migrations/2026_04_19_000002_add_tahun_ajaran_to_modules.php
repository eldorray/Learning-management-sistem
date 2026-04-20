<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Courses
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')->nullable()->after('instructor_id')
                ->constrained('tahun_ajaran')->nullOnDelete();
            $table->index('tahun_ajaran_id');
        });

        // Enrollments
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')->nullable()->after('course_id')
                ->constrained('tahun_ajaran')->nullOnDelete();
            $table->index('tahun_ajaran_id');
        });

        // Tahfidz groups
        Schema::table('tahfidz_groups', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')->nullable()->after('is_active')
                ->constrained('tahun_ajaran')->nullOnDelete();
            $table->index('tahun_ajaran_id');
        });

        // Tahfidz targets
        Schema::table('tahfidz_targets', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')->nullable()->after('keterangan')
                ->constrained('tahun_ajaran')->nullOnDelete();
            $table->index('tahun_ajaran_id');
        });

        // Tahfidz records
        Schema::table('tahfidz_records', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')->nullable()->after('tanggal_setoran')
                ->constrained('tahun_ajaran')->nullOnDelete();
            $table->index('tahun_ajaran_id');
        });
    }

    public function down(): void
    {
        Schema::table('tahfidz_records', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropIndex(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
        });

        Schema::table('tahfidz_targets', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropIndex(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
        });

        Schema::table('tahfidz_groups', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropIndex(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropIndex(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropIndex(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
        });
    }
};
