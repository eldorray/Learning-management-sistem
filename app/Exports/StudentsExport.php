<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected string $search;
    protected string $filterGender;
    protected string $filterClass;

    public function __construct(string $search = '', string $filterGender = '', string $filterClass = '')
    {
        $this->search = $search;
        $this->filterGender = $filterGender;
        $this->filterClass = $filterClass;
    }

    public function query()
    {
        $query = User::where('role', 'student');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterGender) {
            $query->where('gender', $this->filterGender);
        }

        if ($this->filterClass) {
            $query->where('class_group', $this->filterClass);
        }

        return $query->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Lengkap',
            'Email',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'No. Telepon',
            'Alamat',
            'Kelas',
            'Nama Wali',
            'Tanggal Registrasi',
        ];
    }

    public function map($student): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $student->nis ?? '-',
            $student->name,
            $student->email,
            $student->gender === 'L' ? 'Laki-laki' : ($student->gender === 'P' ? 'Perempuan' : '-'),
            $student->birth_date ? $student->birth_date->format('d/m/Y') : '-',
            $student->phone ?? '-',
            $student->address ?? '-',
            $student->class_group ?? '-',
            $student->guardian_name ?? '-',
            $student->created_at->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0058BA'],
                ],
            ],
        ];
    }
}
