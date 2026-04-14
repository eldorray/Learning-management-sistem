<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Carbon\Carbon;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    protected int $imported = 0;
    protected int $skipped = 0;

    public function model(array $row)
    {
        // Check if a student with this email or NIS already exists
        $existing = User::where('email', $row['email'])
            ->orWhere(function ($q) use ($row) {
                if (!empty($row['nis'])) {
                    $q->where('nis', $row['nis']);
                }
            })
            ->first();

        if ($existing) {
            $this->skipped++;
            return null;
        }

        $this->imported++;

        // Parse gender - accept various formats
        $gender = null;
        if (!empty($row['jenis_kelamin'])) {
            $g = strtolower(trim($row['jenis_kelamin']));
            if (in_array($g, ['l', 'laki-laki', 'laki', 'male', 'm'])) {
                $gender = 'L';
            } elseif (in_array($g, ['p', 'perempuan', 'female', 'f', 'wanita'])) {
                $gender = 'P';
            }
        }

        // Parse birth date - accept various formats
        $birthDate = null;
        if (!empty($row['tanggal_lahir'])) {
            try {
                $birthDate = Carbon::parse($row['tanggal_lahir'])->format('Y-m-d');
            } catch (\Exception $e) {
                // Try d/m/Y format
                try {
                    $birthDate = Carbon::createFromFormat('d/m/Y', $row['tanggal_lahir'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $birthDate = null;
                }
            }
        }

        return new User([
            'name' => $row['nama_lengkap'] ?? $row['nama'] ?? '',
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password123'),
            'role' => 'student',
            'nis' => $row['nis'] ?? null,
            'phone' => $row['no_telepon'] ?? $row['telepon'] ?? null,
            'address' => $row['alamat'] ?? null,
            'gender' => $gender,
            'birth_date' => $birthDate,
            'guardian_name' => $row['nama_wali'] ?? $row['wali'] ?? null,
            'class_group' => $row['kelas'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'nama_lengkap' => 'required_without:nama|string',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'email.required' => 'Kolom email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'nama_lengkap.required_without' => 'Kolom nama wajib diisi.',
        ];
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }
}
