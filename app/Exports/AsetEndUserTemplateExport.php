<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AsetEndUserTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                'Laptop Dell Latitude 5420',
                'Komputer & Laptop', // Kategori
                'Dell',
                'Latitude 5420',
                'DL5420-001',
                '15/01/2024',
                12500000,
                'baik', // baik/rusak ringan/rusak berat/hilang
                'Deskripsi laptop',
            ],
            [
                'Printer HP LaserJet',
                'Printer & Scanner',
                'HP',
                'LaserJet Pro M404dn',
                'HPL-M404-001',
                '20/01/2024',
                4500000,
                'baik',
                'Printer untuk dokumen',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Aset *',
            'Kategori * (sesuai nama kategori)',
            'Merek',
            'Tipe/Model',
            'Nomor Seri',
            'Tanggal Perolehan (dd/mm/yyyy)',
            'Nilai Perolehan *',
            'Kondisi * (baik/rusak ringan/rusak berat/hilang)',
            'Deskripsi',
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
