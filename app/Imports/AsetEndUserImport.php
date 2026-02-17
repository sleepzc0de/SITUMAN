<?php

namespace App\Imports;

use App\Models\AsetEndUser;
use App\Models\KategoriAset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Carbon\Carbon;

class AsetEndUserImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
{
    use SkipsFailures, SkipsErrors;

    public function model(array $row)
    {
        // Cari kategori berdasarkan nama
        $kategori = KategoriAset::where('nama', $row['kategori_sesuai_nama_kategori'] ?? $row['kategori'])->first();

        if (!$kategori) {
            return null;
        }

        // Parse tanggal
        $tanggalPerolehan = null;
        if (!empty($row['tanggal_perolehan_ddmmyyyy'] ?? $row['tanggal_perolehan'])) {
            try {
                $tanggalPerolehan = Carbon::createFromFormat('d/m/Y', $row['tanggal_perolehan_ddmmyyyy'] ?? $row['tanggal_perolehan']);
            } catch (\Exception $e) {
                $tanggalPerolehan = null;
            }
        }

        return AsetEndUser::create([
            'kategori_id' => $kategori->id,
            'nama_aset' => $row['nama_aset'] ?? $row['nama'],
            'deskripsi' => $row['deskripsi'] ?? null,
            'merek' => $row['merek'] ?? null,
            'tipe' => $row['tipemodel'] ?? $row['tipe'] ?? null,
            'nomor_seri' => $row['nomor_seri'] ?? null,
            'tanggal_perolehan' => $tanggalPerolehan,
            'nilai_perolehan' => $row['nilai_perolehan'] ?? 0,
            'kondisi' => $row['kondisi_baikrusak_ringanrusak_berathilang'] ?? $row['kondisi'] ?? 'baik',
            'status' => 'tersedia',
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_aset' => 'required|string',
            'kategori' => 'required|string',
            'nilai_perolehan' => 'required|numeric|min:0',
            'kondisi' => 'required|in:baik,rusak ringan,rusak berat,hilang',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_aset.required' => 'Nama Aset harus diisi',
            'kategori.required' => 'Kategori harus diisi',
            'nilai_perolehan.required' => 'Nilai Perolehan harus diisi',
            'kondisi.required' => 'Kondisi harus diisi',
            'kondisi.in' => 'Kondisi harus salah satu dari: baik, rusak ringan, rusak berat, hilang',
        ];
    }
}
