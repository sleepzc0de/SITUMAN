<?php
// database/migrations/2026_02_13_164241_create_spp_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('bulan', 20);
            $table->string('no_spp', 100)->unique();
            $table->string('nominatif', 255)->nullable();
            $table->date('tgl_spp');
            $table->string('jenis_kegiatan', 255);
            $table->string('jenis_belanja', 50);
            $table->string('nomor_kontrak', 255)->nullable();
            $table->string('no_bast', 255)->nullable();
            $table->string('id_eperjadin', 255)->nullable();
            $table->text('uraian_spp');
            $table->string('bagian', 255);
            $table->string('nama_pic', 255);
            $table->string('kode_kegiatan', 50);
            $table->string('kro', 50);
            $table->string('ro', 50);
            $table->string('sub_komponen', 255);
            $table->string('mak', 50);
            $table->string('nomor_surat_tugas', 255)->nullable();
            $table->date('tanggal_st')->nullable();
            $table->string('nomor_undangan', 255)->nullable();
            $table->decimal('bruto', 20, 2);
            $table->decimal('ppn', 20, 2)->default(0);
            $table->decimal('pph', 20, 2)->default(0);
            $table->decimal('netto', 20, 2);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('ls_bendahara', 50);
            $table->string('staff_ppk', 255)->nullable();
            $table->string('no_sp2d', 255)->nullable();
            $table->date('tgl_selesai_sp2d')->nullable();
            $table->date('tgl_sp2d')->nullable();
            $table->string('status', 50);
            $table->string('coa', 100);
            $table->string('posisi_uang', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Index untuk performa
            $table->index(['coa'], 'idx_coa');
            $table->index(['bulan'], 'idx_bulan');
            $table->index(['status'], 'idx_status');
            $table->index(['ro'], 'idx_ro');
            $table->index(['created_at'], 'idx_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spp');
    }
};
