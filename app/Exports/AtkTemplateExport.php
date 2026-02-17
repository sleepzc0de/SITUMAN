<?php

namespace App\Exports;

use App\Models\KategoriAtk;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AtkTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        // Return empty array with sample data
        return [
            [
                'Pulpen Hitam',
                'Alat Tulis', // Kategori (sesuai nama kategori yang ada)
                'Pulpen tinta hitam',
                'pcs',
                50,
                100,
                2500,
            ],
            [
                'Kertas HVS A4',
                'Kertas',
                'Kertas HVS ukuran A4',
                'rim',
                10,
                50,
                45000,
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Nama ATK *',
            'Kategori * (sesuai nama kategori)',
            'Deskripsi',
            'Satuan * (pcs/rim/box/dll)',
            'Stok Minimum *',
            'Stok Tersedia *',
            'Harga Satuan *',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }
}
