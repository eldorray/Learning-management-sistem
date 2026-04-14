<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'admin@lms.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'xp_points' => 0,
            'streak_days' => 0,
        ]);

        // Instructors
        $instructors = [
            ['name' => 'Dr. Siti Aminah', 'email' => 'siti@lms.test'],
            ['name' => 'Ustadz Malik Ibrahim', 'email' => 'malik@lms.test'],
        ];

        $instructorModels = collect($instructors)->map(function ($data) {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'xp_points' => 0,
                'streak_days' => 0,
            ]);
        });

        // Students
        $students = [];
        $studentData = [
            ['name' => 'Budi Santoso', 'email' => 'budi@lms.test', 'xp' => 420, 'streak' => 12],
            ['name' => 'Ani Rahayu', 'email' => 'ani@lms.test', 'xp' => 380, 'streak' => 8],
            ['name' => 'Muhammad Rizki', 'email' => 'rizki@lms.test', 'xp' => 210, 'streak' => 3],
            ['name' => 'Fatimah Zahra', 'email' => 'fatimah@lms.test', 'xp' => 550, 'streak' => 20],
            ['name' => 'Deni Kurniawan', 'email' => 'deni@lms.test', 'xp' => 90, 'streak' => 1],
            ['name' => 'Nur Hidayah', 'email' => 'hidayah@lms.test', 'xp' => 310, 'streak' => 7],
            ['name' => 'Demo Siswa', 'email' => 'student@lms.test', 'xp' => 150, 'streak' => 5],
        ];

        foreach ($studentData as $data) {
            $students[] = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'xp_points' => $data['xp'],
                'streak_days' => $data['streak'],
            ]);
        }

        // Courses
        $coursesData = [
            [
                'title' => 'Dasar-Dasar Pemrograman Web',
                'category' => 'Teknologi',
                'level' => 'beginner',
                'duration' => 240,
                'description' => 'Pelajari dasar-dasar pemrograman web dari nol. Kursus ini mencakup HTML, CSS, dan JavaScript untuk pemula.',
                'short_description' => 'Kuasai fondasi pengembangan web modern dari nol hingga bisa.',
                'modules' => [
                    ['title' => 'Pengenalan HTML', 'lessons' => ['Apa itu HTML?', 'Tag dan Elemen Dasar', 'Formulir dan Input', 'Semantik HTML']],
                    ['title' => 'CSS Styling', 'lessons' => ['Box Model', 'Flexbox', 'Grid Layout', 'Animasi CSS']],
                    ['title' => 'JavaScript Dasar', 'lessons' => ['Variabel dan Tipe Data', 'Fungsi', 'DOM Manipulation', 'Event Handling']],
                ],
            ],
            [
                'title' => 'Fiqih Muamalah Kontemporer',
                'category' => 'Agama',
                'level' => 'intermediate',
                'duration' => 180,
                'description' => 'Kajian mendalam tentang hukum-hukum muamalah dalam perspektif Islam kontemporer.',
                'short_description' => 'Memahami fiqih muamalah untuk kehidupan ekonomi modern yang berkah.',
                'modules' => [
                    ['title' => 'Dasar Muamalah', 'lessons' => ['Pengertian dan Ruang Lingkup', 'Prinsip-prinsip Muamalah', 'Akad dalam Islam']],
                    ['title' => 'Perbankan Syariah', 'lessons' => ['Konsep Perbankan Islam', 'Produk Perbankan Syariah', 'Mudharabah dan Musyarakah']],
                    ['title' => 'Zakat dan Wakaf', 'lessons' => ['Zakat Profesi', 'Wakaf Produktif', 'Implementasi Modern']],
                ],
            ],
            [
                'title' => 'Bahasa Arab untuk Pemula',
                'category' => 'Bahasa',
                'level' => 'beginner',
                'duration' => 300,
                'description' => 'Kursus bahasa Arab komprehensif untuk pemula yang ingin menguasai dasar-dasar bahasa Al-Quran.',
                'short_description' => 'Mulai perjalanan memahami bahasa Arab dan Al-Quran dengan mudah.',
                'modules' => [
                    ['title' => 'Huruf Hijaiyah', 'lessons' => ['Mengenal Huruf', 'Harakat dan Tanwin', 'Membaca Kata Dasar']],
                    ['title' => 'Kosakata Dasar', 'lessons' => ['Kata Benda', 'Kata Kerja', 'Percakapan Sehari-hari']],
                ],
            ],
            [
                'title' => 'Leadership dan Manajemen Organisasi',
                'category' => 'Manajemen',
                'level' => 'intermediate',
                'duration' => 200,
                'description' => 'Kembangkan kemampuan kepemimpinan dan manajemen organisasi yang efektif dalam perspektif Islam.',
                'short_description' => 'Jadilah pemimpin efektif dengan prinsip-prinsip manajemen Islam.',
                'modules' => [
                    ['title' => 'Dasar Kepemimpinan', 'lessons' => ['Teori Kepemimpinan', 'Gaya Kepemimpinan', 'Komunikasi Efektif']],
                    ['title' => 'Manajemen Tim', 'lessons' => ['Membangun Tim', 'Resolusi Konflik', 'Delegasi Tugas']],
                ],
            ],
            [
                'title' => 'Tahsin Al-Quran Level Lanjut',
                'category' => 'Agama',
                'level' => 'advanced',
                'duration' => 400,
                'description' => 'Penyempurnaan bacaan Al-Quran dengan tajwid yang benar untuk tingkat lanjut.',
                'short_description' => 'Sempurnakan bacaan Al-Quran dengan ilmu tajwid tingkat mahir.',
                'modules' => [
                    ['title' => 'Makharijul Huruf', 'lessons' => ['Sifat Huruf', 'Latihan Pengucapan', 'Evaluasi']],
                    ['title' => 'Hukum Tajwid', 'lessons' => ['Mad dan Qasr', 'Waqaf dan Ibtida', 'Praktik Murotal']],
                ],
            ],
        ];

        $createdCourses = [];
        foreach ($coursesData as $index => $courseData) {
            $instructor = $instructorModels[$index % $instructorModels->count()];

            $course = Course::create([
                'title' => $courseData['title'],
                'slug' => Str::slug($courseData['title']),
                'description' => $courseData['description'],
                'short_description' => $courseData['short_description'],
                'category' => $courseData['category'],
                'level' => $courseData['level'],
                'duration_minutes' => $courseData['duration'],
                'is_free' => true,
                'is_published' => true,
                'instructor_id' => $instructor->id,
                'total_lessons' => collect($courseData['modules'])->sum(fn($m) => count($m['lessons'])),
            ]);

            $moduleOrder = 1;
            foreach ($courseData['modules'] as $moduleData) {
                $module = Module::create([
                    'course_id' => $course->id,
                    'title' => $moduleData['title'],
                    'order' => $moduleOrder++,
                ]);

                $lessonOrder = 1;
                foreach ($moduleData['lessons'] as $lessonTitle) {
                    Lesson::create([
                        'module_id' => $module->id,
                        'course_id' => $course->id,
                        'title' => $lessonTitle,
                        'content' => "Ini adalah konten untuk pelajaran \"{$lessonTitle}\". Materi ini dirancang untuk memberikan pemahaman mendalam tentang topik yang dibahas. Pelajari dengan seksama dan jangan ragu untuk mengulang jika diperlukan.",
                        'type' => 'text',
                        'duration_minutes' => rand(10, 30),
                        'order' => $lessonOrder++,
                    ]);
                }
            }

            $createdCourses[] = $course;
        }

        // Enrollments for demo student
        $demoStudent = collect($students)->last(); // student@lms.test
        foreach (array_slice($createdCourses, 0, 3) as $i => $course) {
            $progress = [72, 45, 10][$i] ?? 0;
            Enrollment::create([
                'user_id' => $demoStudent->id,
                'course_id' => $course->id,
                'enrolled_at' => now()->subDays(rand(5, 30)),
                'progress_percentage' => $progress,
                'status' => $progress >= 100 ? 'completed' : 'active',
            ]);
        }

        // Random enrollments for other students
        foreach ($students as $student) {
            if ($student->id === $demoStudent->id) continue;

            $coursesToEnroll = array_slice($createdCourses, 0, rand(1, 4));
            foreach ($coursesToEnroll as $course) {
                $progress = rand(0, 100);
                Enrollment::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'enrolled_at' => now()->subDays(rand(1, 60)),
                    'progress_percentage' => $progress,
                    'status' => $progress >= 100 ? 'completed' : 'active',
                    'completed_at' => $progress >= 100 ? now()->subDays(rand(0, 10)) : null,
                ]);
            }
        }
    }
}
