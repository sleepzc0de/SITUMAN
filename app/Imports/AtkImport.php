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
        // Cari kategori berdasarkan nama
        $kategori = KategoriAtk::where('nama', $row['kategori_sesuai_nama_kategori'] ?? $row['kategori'])->first();

        if (!$kategori) {
            return null;
        }

        $atk = Atk::create([
            'kategori_id' => $kategori->id,
            'nama' => $row['nama_atk'] ?? $row['nama'],
            'deskripsi' => $row['deskripsi'] ?? null,
            'satuan' => $row['satuan_pcsrimboxdll'] ?? $row['satuan'],
            'stok_minimum' => $row['stok_minimum'] ?? 0,
            'stok_tersedia' => $row['stok_tersedia'] ?? 0,
            'harga_satuan' => $row['harga_satuan'] ?? 0,
        ]);

        $atk->updateStatus();

        return $atk;
    }

    public function rules(): array
    {
        return [
            'nama_atk' => 'required|string',
            'kategori' => 'required|string',
            'satuan' => 'required|string',
            'stok_minimum' => 'required|numeric|min:0',
            'stok_tersedia' => 'required|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_atk.required' => 'Nama ATK harus diisi',
            'kategori.required' => 'Kategori harus diisi',
            'satuan.required' => 'Satuan harus diisi',
        ];
    }
}
