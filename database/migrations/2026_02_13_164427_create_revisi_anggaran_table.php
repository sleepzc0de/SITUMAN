<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisi_anggaran', function (Blueprint $table) {
            $table->id();

            // Pastikan tipe data anggaran_id sesuai dengan tipe id di tabel anggaran
            // Asumsi anggaran.id menggunakan bigIncrements/unsignedBigInteger
            $table->unsignedBigInteger('anggaran_id');

            $table->string('jenis_revisi', 100);
            $table->decimal('pagu_sebelum', 20, 2);
            $table->decimal('pagu_sesudah', 20, 2);
            $table->text('alasan_revisi');
            $table->date('tanggal_revisi');
            $table->string('dokumen_pendukung', 500)->nullable();

            // Ubah tipe data menjadi UUID agar sesuai dengan users.id
            $table->uuid('user_id');

            $table->timestamps();

            // Foreign key untuk anggaran
            $table->foreign('anggaran_id')
                  ->references('id')
                  ->on('anggaran')
                  ->onDelete('cascade');

            // Foreign key untuk users
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisi_anggaran');
    }
};
