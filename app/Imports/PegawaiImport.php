<?php

namespace App\Imports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class PegawaiImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithBatchInserts, WithChunkReading
{
    use SkipsErrors;

    public function model(array $row): ?Pegawai
    {
        if (empty($row['nip']) || empty($row['nama'])) {
            return null;
        }

        return Pegawai::updateOrCreate(
            ['nip' => (string) $row['nip']],
            [
                'nama'            => $row['nama'] ?? null,
                'nama_gelar'      => $row['nama_gelar'] ?? null,
                'pangkat'         => $row['pangkat'] ?? null,
                'pendidikan'      => $row['pendidikan'] ?? null,
                'email_kemenkeu'  => $row['email_kemenkeu'] ?? null,
                'email_pribadi'   => $row['email_pribadi'] ?? null,
                'no_hp'           => isset($row['no_hp']) ? (string)$row['no_hp'] : null,
                'grading'         => isset($row['grading']) ? (int)$row['grading'] : null,
                'jabatan'         => $row['jabatan'] ?? null,
                'jenis_jabatan'   => $row['jenis_jabatan'] ?? null,
                'nama_jabatan'    => $row['nama_jabatan'] ?? null,
                'eselon'          => $row['eselon'] ?? null,
                'jenis_pegawai'   => $row['jenis_pegawai'] ?? null,
                'status'          => $row['status'] ?? 'AKTIF',
                'lokasi'          => $row['lokasi'] ?? null,
                'bagian'          => $row['bagian'] ?? null,
                'subbagian'       => $row['subbagian'] ?? null,
                'jurusan_s1'      => $row['jurusan_s1'] ?? null,
                'jurusan_s2'      => $row['jurusan_s2'] ?? null,
                'jurusan_s3'      => $row['jurusan_s3'] ?? null,
                'tmt_cpns'        => $this->parseDate($row['tmt_cpns'] ?? null),
                'masa_kerja_tahun'=> isset($row['masa_kerja_tahun']) ? (int)$row['masa_kerja_tahun'] : null,
                'masa_kerja_bulan'=> isset($row['masa_kerja_bulan']) ? (int)$row['masa_kerja_bulan'] : null,
                'tanggal_lahir'   => $this->parseDate($row['tanggal_lahir'] ?? null),
                'bulan_lahir'     => $row['bulan_lahir'] ?? null,
                'tahun_lahir'     => isset($row['tahun_lahir']) ? (int)$row['tahun_lahir'] : null,
                'usia'            => isset($row['usia']) ? (int)$row['usia'] : null,
                'tanggal_pensiun' => $this->parseDate($row['tanggal_pensiun'] ?? null),
                'tahun_pensiun'   => isset($row['tahun_pensiun']) ? (int)$row['tahun_pensiun'] : null,
                'proyeksi_kp_1'   => $row['proyeksi_kp_1'] ?? null,
                'proyeksi_kp_2'   => $row['proyeksi_kp_2'] ?? null,
                'keterangan_kp'   => $row['keterangan_kp'] ?? null,
                'jenis_kelamin'   => $row['jenis_kelamin'] ?? null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nip'  => 'required',
            'nama' => 'required',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 200;
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) return null;

        try {
            if (is_numeric($value)) {
                return Carbon::createFromFormat('d/m/Y', Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('d/m/Y'))->toDateString();
            }
            return Carbon::parse($value)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
