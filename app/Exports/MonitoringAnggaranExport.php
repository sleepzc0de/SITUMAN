<?php

namespace App\Exports;

use App\Models\Anggaran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class MonitoringAnggaranExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize,
    WithColumnFormatting
{
    public function __construct(
        private Collection $data,
        private string $ro = 'all'
    ) {}

    public function collection(): Collection
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Monitoring Anggaran';
    }

    public function headings(): array
    {
        return [
            'RO', 'Sub Komponen', 'Kode Akun', 'Program / Kegiatan',
            'Pagu Anggaran',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
            'Outstanding', 'Total Realisasi', 'Sisa', '% Penyerapan',
        ];
    }

    public function map($row): array
    {
        $pct = $row->pagu_anggaran > 0
            ? round(($row->total_penyerapan / $row->pagu_anggaran) * 100, 2)
            : 0;

        return [
            $row->ro,
            $row->kode_subkomponen ?? '-',
            $row->kode_akun        ?? '-',
            $row->program_kegiatan,
            (float) $row->pagu_anggaran,
            (float) $row->januari,
            (float) $row->februari,
            (float) $row->maret,
            (float) $row->april,
            (float) $row->mei,
            (float) $row->juni,
            (float) $row->juli,
            (float) $row->agustus,
            (float) $row->september,
            (float) $row->oktober,
            (float) $row->november,
            (float) $row->desember,
            (float) $row->tagihan_outstanding,
            (float) $row->total_penyerapan,
            (float) $row->sisa,
            $pct . '%',
        ];
    }

    public function columnFormats(): array
    {
        // Kolom E s.d. T = angka rupiah
        $rupiahFormat = '#,##0';
        return [
            'E'  => $rupiahFormat,
            'F'  => $rupiahFormat,
            'G'  => $rupiahFormat,
            'H'  => $rupiahFormat,
            'I'  => $rupiahFormat,
            'J'  => $rupiahFormat,
            'K'  => $rupiahFormat,
            'L'  => $rupiahFormat,
            'M'  => $rupiahFormat,
            'N'  => $rupiahFormat,
            'O'  => $rupiahFormat,
            'P'  => $rupiahFormat,
            'Q'  => $rupiahFormat,
            'R'  => $rupiahFormat,
            'S'  => $rupiahFormat,
            'T'  => $rupiahFormat,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row bold + background navy
            1 => [
                'font'    => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'    => [
                    'fillType'   => 'solid',
                    'startColor' => ['rgb' => '334E68'],
                ],
                'alignment' => ['wrapText' => true],
            ],
        ];
    }
}
