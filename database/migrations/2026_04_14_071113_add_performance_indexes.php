<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // lesson_progress: queries often filter by course_id + is_completed
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->index(['course_id', 'is_completed'], 'lp_course_completed_idx');
        });

        // enrollments: queries often filter by course_id + status (Analytics, Dashboard)
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['course_id', 'status'], 'enr_course_status_idx');
        });

        // quiz_answers: queries filter by user_id + lesson_id
        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->index(['user_id', 'lesson_id'], 'qa_user_lesson_idx');
        });

        // login_logs: queries order by logged_in_at and filter by user_id
        Schema::table('login_logs', function (Blueprint $table) {
            $table->index('logged_in_at', 'll_logged_in_at_idx');
            $table->index('user_id', 'll_user_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropIndex('lp_course_completed_idx');
        });
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('enr_course_status_idx');
        });
        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->dropIndex('qa_user_lesson_idx');
        });
        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropIndex('ll_logged_in_at_idx');
            $table->dropIndex('ll_user_id_idx');
        });
    }
};
