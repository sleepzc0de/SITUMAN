<?php

namespace App\Exports;

use App\Models\Atk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AtkExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Atk::with('kategori')->get();
    }

    public function headings(): array
    {
        return [
            'Kode ATK',
            'Nama ATK',
            'Kategori',
            'Deskripsi',
            'Satuan',
            'Stok Minimum',
            'Stok Tersedia',
            'Harga Satuan',
            'Status',
            'Dibuat Tanggal',
        ];
    }

    public function map($atk): array
    {
        return [
            $atk->kode_atk,
            $atk->nama,
            $atk->kategori->nama,
            $atk->deskripsi,
            $atk->satuan,
            $atk->stok_minimum,
            $atk->stok_tersedia,
            $atk->harga_satuan,
            ucfirst($atk->status),
            $atk->created_at->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
