<?php

namespace App\Exports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PegawaiExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Pegawai::query();

        if (!empty($this->filters['bagian'])) {
            $query->where('bagian', $this->filters['bagian']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (!empty($this->filters['jenis_kelamin'])) {
            $query->where('jenis_kelamin', $this->filters['jenis_kelamin']);
        }

        return $query->orderBy('nama');
    }

    public function title(): string
    {
        return 'Data Pegawai';
    }

    public function headings(): array
    {
        return [
            'No', 'Nama', 'Nama Gelar', 'NIP', 'Pangkat', 'Pendidikan',
            'Email Kemenkeu', 'Email Pribadi', 'No HP', 'Grading',
            'Jabatan', 'Jenis Jabatan', 'Nama Jabatan', 'Eselon',
            'Jenis Pegawai', 'Status', 'Lokasi', 'Bagian', 'Subbagian',
            'Jurusan S1', 'Jurusan S2', 'Jurusan S3',
            'TMT CPNS', 'Masa Kerja (Tahun)', 'Masa Kerja (Bulan)',
            'Tanggal Lahir', 'Bulan Lahir', 'Tahun Lahir', 'Usia',
            'Tanggal Pensiun', 'Tahun Pensiun',
            'Proyeksi KP 1', 'Proyeksi KP 2', 'Keterangan KP',
            'Jenis Kelamin',
        ];
    }

    public function map($pegawai): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $pegawai->nama,
            $pegawai->nama_gelar,
            $pegawai->nip,
            $pegawai->pangkat,
            $pegawai->pendidikan,
            $pegawai->email_kemenkeu,
            $pegawai->email_pribadi,
            $pegawai->no_hp,
            $pegawai->grading,
            $pegawai->jabatan,
            $pegawai->jenis_jabatan,
            $pegawai->nama_jabatan,
            $pegawai->eselon,
            $pegawai->jenis_pegawai,
            $pegawai->status,
            $pegawai->lokasi,
            $pegawai->bagian,
            $pegawai->subbagian,
            $pegawai->jurusan_s1,
            $pegawai->jurusan_s2,
            $pegawai->jurusan_s3,
            $pegawai->tmt_cpns ? $pegawai->tmt_cpns->format('d/m/Y') : '',
            $pegawai->masa_kerja_tahun,
            $pegawai->masa_kerja_bulan,
            $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('d/m/Y') : '',
            $pegawai->bulan_lahir,
            $pegawai->tahun_lahir,
            $pegawai->usia,
            $pegawai->tanggal_pensiun ? $pegawai->tanggal_pensiun->format('d/m/Y') : '',
            $pegawai->tahun_pensiun,
            $pegawai->proyeksi_kp_1,
            $pegawai->proyeksi_kp_2,
            $pegawai->keterangan_kp,
            $pegawai->jenis_kelamin,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1e3a5f']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
