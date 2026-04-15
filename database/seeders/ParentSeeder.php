<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', 'student')->get();

        if ($students->isEmpty()) return;

        // Create sample parent accounts and link to students
        $parentData = [
            [
                'name'     => 'Hasan Santoso',
                'email'    => 'hasan.ortu@lms.test',
                'children' => [0], // index dalam $students
            ],
            [
                'name'     => 'Siti Rahayu',
                'email'    => 'siti.ortu@lms.test',
                'children' => [1, 2], // punya 2 anak
            ],
            [
                'name'     => 'Bapak Demo Ortu',
                'email'    => 'parent@lms.test',
                'children' => [6], // Demo Siswa
            ],
        ];

        foreach ($parentData as $data) {
            $parent = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'       => $data['name'],
                    'password'   => Hash::make('password'),
                    'role'       => 'parent',
                    'xp_points'  => 0,
                    'streak_days'=> 0,
                ]
            );

            // Link children
            foreach ($data['children'] as $childIndex) {
                $student = $students->get($childIndex);
                if ($student) {
                    $parent->children()->syncWithoutDetaching([
                        $student->id => ['hubungan' => 'orang_tua'],
                    ]);
                }
            }
        }
    }
}
