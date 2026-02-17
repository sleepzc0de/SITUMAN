<?php

namespace App\Exports;

use App\Models\AsetEndUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AsetEndUserExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return AsetEndUser::with(['kategori', 'pegawai'])->get();
    }

    public function headings(): array
    {
        return [
            'Kode Aset',
            'Nama Aset',
            'Kategori',
            'Merek',
            'Tipe',
            'Nomor Seri',
            'Tanggal Perolehan',
            'Nilai Perolehan',
            'Kondisi',
            'Status',
            'Pengguna',
            'Tanggal Peminjaman',
        ];
    }

    public function map($aset): array
    {
        return [
            $aset->kode_aset,
            $aset->nama_aset,
            $aset->kategori->nama,
            $aset->merek,
            $aset->tipe,
            $aset->nomor_seri,
            $aset->tanggal_perolehan ? $aset->tanggal_perolehan->format('d/m/Y') : '',
            $aset->nilai_perolehan,
            ucfirst($aset->kondisi),
            ucfirst($aset->status),
            $aset->pegawai?->nama ?? '',
            $aset->tanggal_peminjaman ? $aset->tanggal_peminjaman->format('d/m/Y') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
