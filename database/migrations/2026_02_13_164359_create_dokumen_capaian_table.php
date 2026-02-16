<?php
// database/migrations/2026_02_13_164359_create_dokumen_capaian_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_capaian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ro', 50);
            $table->string('sub_komponen', 255);
            $table->string('bulan', 20);
            $table->string('nama_dokumen', 255);
            $table->text('file_path')->nullable();
            $table->json('files')->nullable();
            $table->text('keterangan')->nullable();
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Index
            $table->index(['ro'], 'idx_ro');
            $table->index(['bulan'], 'idx_bulan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_capaian');
    }
};
