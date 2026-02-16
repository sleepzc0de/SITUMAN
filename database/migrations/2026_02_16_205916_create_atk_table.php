<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kategori_id')->constrained('kategori_atk')->onDelete('cascade');
            $table->string('kode_atk')->unique();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('satuan'); // pcs, rim, box, dll
            $table->integer('stok_minimum')->default(10);
            $table->integer('stok_tersedia')->default(0);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->enum('status', ['tersedia', 'kosong', 'menipis'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atk');
    }
};
