<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_capaian', function (Blueprint $table) {
            $table->id();
            $table->string('ro', 50);
            $table->string('sub_komponen', 255);
            $table->string('bulan', 20);
            $table->string('nama_dokumen', 255);
            $table->string('file_path', 500);
            $table->text('keterangan')->nullable();

            // Ubah tipe data menjadi UUID agar sesuai dengan users.id
            $table->uuid('user_id');

            $table->timestamps();

            // Tambahkan foreign key langsung di sini
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_capaian');
    }
};
