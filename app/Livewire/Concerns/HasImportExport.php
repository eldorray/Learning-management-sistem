<?php

namespace App\Livewire\Concerns;

use Maatwebsite\Excel\Facades\Excel;

/**
 * Shared import/export modal state and methods for directory components.
 * The using class must define:
 *   - getExportInstance(): the Export class instance
 *   - getExportFilename(): string filename
 *   - getImportInstance(): the Import class instance (with getImportedCount/getSkippedCount/failures)
 *   - entityLabel(): string e.g. 'siswa' or 'guru'
 */
trait HasImportExport
{
    public bool $showImportModal = false;
    public $importFile = null;
    public string $importResult = '';
    public string $importResultType = '';

    public function exportExcel()
    {
        return Excel::download($this->getExportInstance(), $this->getExportFilename());
    }

    public function openImportModal(): void
    {
        $this->importFile       = null;
        $this->importResult     = '';
        $this->importResultType = '';
        $this->showImportModal  = true;
    }

    public function runImport(): void
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'importFile.required' => 'File Excel wajib dipilih.',
            'importFile.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'importFile.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $import = $this->getImportInstance();
            Excel::import($import, $this->importFile->getRealPath());

            $imported = $import->getImportedCount();
            $skipped  = $import->getSkippedCount();
            $failures = $import->failures();
            $label    = $this->entityLabel();

            $message = "{$imported} {$label} berhasil diimpor.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati (duplikat).";
            }
            if ($failures->count() > 0) {
                $message .= " {$failures->count()} baris gagal validasi.";
            }

            $this->importResult     = $message;
            $this->importResultType = $failures->count() > 0 ? 'warning' : 'success';

            if ($imported > 0) {
                session()->flash('success', $message);
            }
        } catch (\Exception $e) {
            $this->importResult     = 'Terjadi kesalahan: ' . $e->getMessage();
            $this->importResultType = 'error';
        }
    }
}
