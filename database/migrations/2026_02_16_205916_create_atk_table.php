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
            $table->string('kode_atk', 50)->unique();
            $table->string('nama', 255);
            $table->string('deskripsi', 2000)->nullable(); // dibatasi 2000 char
            $table->string('satuan', 20);
            $table->unsignedInteger('stok_minimum')->default(10);
            $table->unsignedInteger('stok_tersedia')->default(0);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->enum('status', ['tersedia', 'kosong', 'menipis'])->default('tersedia');
            $table->timestamps();

            $table->index(['kategori_id', 'status']);
            $table->index('nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atk');
    }
};
