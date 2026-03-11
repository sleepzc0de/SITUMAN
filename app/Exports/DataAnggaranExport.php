<?php
// app/Exports/DataAnggaranExport.php

namespace App\Exports;

use App\Models\Anggaran;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DataAnggaranExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    private ?string $ro;
    private ?string $level;

    public function __construct(?string $ro = null, ?string $level = null)
    {
        $this->ro    = $ro;
        $this->level = $level;
    }

    public function query()
    {
        $query = Anggaran::query();

        if ($this->ro && $this->ro !== 'all') {
            $query->where('ro', $this->ro);
        }

        if ($this->level === 'ro') {
            $query->whereNull('kode_subkomponen')->whereNull('kode_akun');
        } elseif ($this->level === 'subkomponen') {
            $query->whereNotNull('kode_subkomponen')->whereNull('kode_akun');
        } elseif ($this->level === 'akun') {
            $query->whereNotNull('kode_akun');
        }

        return $query->orderBy('ro')->orderBy('kode_subkomponen')->orderBy('kode_akun');
    }

    public function headings(): array
    {
        return [
            'kegiatan', 'kro', 'ro', 'kode_subkomponen', 'kode_akun',
            'program_kegiatan', 'pic', 'pagu_anggaran',
            'total_penyerapan', 'tagihan_outstanding', 'sisa',
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];
    }

    public function map($row): array
    {
        return [
            $row->kegiatan,
            $row->kro,
            $row->ro,
            $row->kode_subkomponen ?? '',
            $row->kode_akun ?? '',
            $row->program_kegiatan,
            $row->pic,
            $row->pagu_anggaran,
            $row->total_penyerapan,
            $row->tagihan_outstanding,
            $row->sisa,
            $row->januari,  $row->februari, $row->maret,
            $row->april,    $row->mei,      $row->juni,
            $row->juli,     $row->agustus,  $row->september,
            $row->oktober,  $row->november, $row->desember,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1e3a5f']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, 'B' => 8,  'C' => 8,
            'D' => 18, 'E' => 12, 'F' => 50,
            'G' => 12, 'H' => 18, 'I' => 18,
            'J' => 20, 'K' => 18,
        ];
    }

    public function title(): string
    {
        return 'Data Anggaran';
    }
}
