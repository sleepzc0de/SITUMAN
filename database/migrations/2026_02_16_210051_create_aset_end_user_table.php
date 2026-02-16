<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_end_user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kategori_id')->constrained('kategori_aset')->onDelete('cascade');
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            $table->text('deskripsi')->nullable();
            $table->string('merek')->nullable();
            $table->string('tipe')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->date('tanggal_perolehan')->nullable();
            $table->decimal('nilai_perolehan', 15, 2)->default(0);
            $table->enum('kondisi', ['baik', 'rusak ringan', 'rusak berat', 'hilang'])->default('baik');
            $table->enum('status', ['tersedia', 'dipinjam', 'diperbaiki', 'tidak aktif'])->default('tersedia');
            $table->foreignUuid('pegawai_id')->nullable()->constrained('pegawai')->onDelete('no action');
            $table->date('tanggal_peminjaman')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_end_user');
    }
};
