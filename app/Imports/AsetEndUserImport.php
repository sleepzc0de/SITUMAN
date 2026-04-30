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
        $kategori = KategoriAset::where('nama', $row['kategori_sesuai_nama_kategori'] ?? $row['kategori'])->first();
        if (!$kategori) {
            return null;
        }

        $tanggalPerolehan = null;
        if (!empty($row['tanggal_perolehan_ddmmyyyy'] ?? $row['tanggal_perolehan'])) {
            try {
                $tanggalPerolehan = Carbon::createFromFormat('d/m/Y', $row['tanggal_perolehan_ddmmyyyy'] ?? $row['tanggal_perolehan']);
            } catch (\Exception $e) {
                $tanggalPerolehan = null;
            }
        }

        return AsetEndUser::create([
            'kategori_id'       => $kategori->id,
            'nama_aset'         => mb_substr($row['nama_aset'] ?? $row['nama'] ?? '', 0, 255),
            'deskripsi'         => isset($row['deskripsi']) ? mb_substr($row['deskripsi'], 0, 2000) : null,
            'merek'             => isset($row['merek']) ? mb_substr($row['merek'], 0, 100) : null,
            'tipe'              => isset($row['tipemodel']) ? mb_substr($row['tipemodel'], 0, 100)
                                   : (isset($row['tipe']) ? mb_substr($row['tipe'], 0, 100) : null),
            'nomor_seri'        => isset($row['nomor_seri']) ? mb_substr($row['nomor_seri'], 0, 100) : null,
            'tanggal_perolehan' => $tanggalPerolehan,
            'nilai_perolehan'   => min(99999999999, max(0, (float)($row['nilai_perolehan'] ?? 0))),
            'kondisi'           => $row['kondisi_baikrusak_ringanrusak_berathilang'] ?? $row['kondisi'] ?? 'baik',
            'status'            => 'tersedia',
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_aset'       => 'required|string|min:3|max:255',
            'kategori'        => 'required|string|max:100',
            'deskripsi'       => 'nullable|string|max:2000',
            'merek'           => 'nullable|string|max:100',
            'tipemodel'       => 'nullable|string|max:100',
            'tipe'            => 'nullable|string|max:100',
            'nomor_seri'      => 'nullable|string|max:100',
            'nilai_perolehan' => 'required|numeric|min:0|max:99999999999',
            'kondisi'         => 'required|in:baik,rusak ringan,rusak berat,hilang',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_aset.required'      => 'Nama Aset harus diisi',
            'nama_aset.min'           => 'Nama Aset minimal 3 karakter',
            'nama_aset.max'           => 'Nama Aset maksimal 255 karakter',
            'kategori.required'       => 'Kategori harus diisi',
            'kategori.max'            => 'Nama kategori maksimal 100 karakter',
            'deskripsi.max'           => 'Deskripsi maksimal 2000 karakter',
            'merek.max'               => 'Merek maksimal 100 karakter',
            'tipe.max'                => 'Tipe maksimal 100 karakter',
            'tipemodel.max'           => 'Tipe/Model maksimal 100 karakter',
            'nomor_seri.max'          => 'Nomor seri maksimal 100 karakter',
            'nilai_perolehan.required' => 'Nilai Perolehan harus diisi',
            'nilai_perolehan.max'     => 'Nilai Perolehan terlalu besar',
            'kondisi.required'        => 'Kondisi harus diisi',
            'kondisi.in'              => 'Kondisi harus salah satu dari: baik, rusak ringan, rusak berat, hilang',
        ];
    }
}
