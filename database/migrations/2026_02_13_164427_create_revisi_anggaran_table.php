<?php
// database/migrations/2026_02_13_164427_create_revisi_anggaran_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisi_anggaran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('anggaran_id');
            $table->string('jenis_revisi', 100);
            $table->decimal('pagu_sebelum', 20, 2);
            $table->decimal('pagu_sesudah', 20, 2);
            $table->text('alasan_revisi');
            $table->date('tanggal_revisi');
            $table->string('dokumen_pendukung', 500)->nullable();
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('anggaran_id')
                  ->references('id')
                  ->on('anggaran')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Index
            $table->index(['jenis_revisi'], 'idx_jenis');
            $table->index(['tanggal_revisi'], 'idx_tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisi_anggaran');
    }
};
