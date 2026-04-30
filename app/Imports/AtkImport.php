<?php
namespace App\Imports;

use App\Models\Atk;
use App\Models\KategoriAtk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class AtkImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
{
    use SkipsFailures, SkipsErrors;

    public function model(array $row)
    {
        $kategori = KategoriAtk::where('nama', $row['kategori_sesuai_nama_kategori'] ?? $row['kategori'])->first();
        if (!$kategori) {
            return null;
        }

        $atk = Atk::create([
            'kategori_id'   => $kategori->id,
            'nama'          => mb_substr($row['nama_atk'] ?? $row['nama'] ?? '', 0, 255),
            'deskripsi'     => isset($row['deskripsi']) ? mb_substr($row['deskripsi'], 0, 2000) : null,
            'satuan'        => mb_substr($row['satuan_pcsrimboxdll'] ?? $row['satuan'] ?? '', 0, 20),
            'stok_minimum'  => min(999999, max(0, (int)($row['stok_minimum'] ?? 0))),
            'stok_tersedia' => min(999999, max(0, (int)($row['stok_tersedia'] ?? 0))),
            'harga_satuan'  => min(99999999999, max(0, (float)($row['harga_satuan'] ?? 0))),
        ]);

        $atk->updateStatus();
        return $atk;
    }

    public function rules(): array
    {
        return [
            'nama_atk'      => 'required|string|min:3|max:255',
            'kategori'      => 'required|string|max:100',
            'deskripsi'     => 'nullable|string|max:2000',
            'satuan'        => 'required|string|max:20',
            'stok_minimum'  => 'required|numeric|min:0|max:999999',
            'stok_tersedia' => 'required|numeric|min:0|max:999999',
            'harga_satuan'  => 'required|numeric|min:0|max:99999999999',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_atk.required'      => 'Nama ATK harus diisi',
            'nama_atk.min'           => 'Nama ATK minimal 3 karakter',
            'nama_atk.max'           => 'Nama ATK maksimal 255 karakter',
            'kategori.required'      => 'Kategori harus diisi',
            'kategori.max'           => 'Nama kategori maksimal 100 karakter',
            'deskripsi.max'          => 'Deskripsi maksimal 2000 karakter',
            'satuan.required'        => 'Satuan harus diisi',
            'satuan.max'             => 'Satuan maksimal 20 karakter',
            'stok_minimum.max'       => 'Stok minimum maksimal 999.999',
            'stok_tersedia.max'      => 'Stok tersedia maksimal 999.999',
            'harga_satuan.max'       => 'Harga satuan terlalu besar',
        ];
    }
}
