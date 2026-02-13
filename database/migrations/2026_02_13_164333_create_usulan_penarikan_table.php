<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usulan_penarikan', function (Blueprint $table) {
            $table->id();
            $table->string('ro', 50);
            $table->string('sub_komponen', 255);
            $table->string('bulan', 20);
            $table->decimal('nilai_usulan', 20, 2);
            $table->text('keterangan')->nullable();
            $table->string('status', 20)->default('pending');

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
        Schema::dropIfExists('usulan_penarikan');
    }
};
