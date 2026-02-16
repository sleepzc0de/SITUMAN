<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_aset', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('aset_id')->constrained('aset_end_user')->onDelete('cascade');
            $table->foreignUuid('pegawai_id')->nullable()->constrained('pegawai')->onDelete('no action');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('no action');
            $table->enum('jenis_aktivitas', ['peminjaman', 'pengembalian', 'perbaikan', 'mutasi', 'penghapusan']);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_aset');
    }
};
