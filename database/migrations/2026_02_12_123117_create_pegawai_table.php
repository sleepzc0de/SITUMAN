<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('nama_gelar')->nullable();
            $table->string('nip')->unique();
            $table->string('pangkat')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('email_kemenkeu')->nullable();
            $table->string('email_pribadi')->nullable();
            $table->string('no_hp')->nullable();
            $table->integer('grading')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('jenis_jabatan')->nullable();
            $table->string('nama_jabatan')->nullable();
            $table->string('eselon')->nullable();
            $table->string('jenis_pegawai')->nullable();
            $table->string('status')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('bagian')->nullable();
            $table->string('subbagian')->nullable();
            $table->string('jurusan_s1')->nullable();
            $table->string('jurusan_s2')->nullable();
            $table->string('jurusan_s3')->nullable();
            $table->date('tmt_cpns')->nullable();
            $table->integer('masa_kerja_tahun')->nullable();
            $table->integer('masa_kerja_bulan')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('bulan_lahir')->nullable();
            $table->integer('tahun_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->date('tanggal_pensiun')->nullable();
            $table->integer('tahun_pensiun')->nullable();
            $table->string('proyeksi_kp_1')->nullable();
            $table->string('proyeksi_kp_2')->nullable();
            $table->text('keterangan_kp')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->timestamps();

            $table->index(['bagian', 'subbagian']);
            $table->index('status');
            $table->index('grading');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
