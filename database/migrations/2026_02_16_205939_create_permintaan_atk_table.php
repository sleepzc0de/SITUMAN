<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_atk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor_permintaan', 50)->unique();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('no action');
            $table->foreignUuid('pegawai_id')->nullable()->constrained('pegawai')->onDelete('no action');
            $table->date('tanggal_permintaan');
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'selesai'])->default('pending');
            $table->string('keterangan', 1000)->nullable();
            $table->string('alasan_penolakan', 500)->nullable();
            $table->foreignUuid('disetujui_oleh')->nullable()->constrained('users')->onDelete('no action');
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamps();

            $table->index(['status', 'tanggal_permintaan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_atk');
    }
};
