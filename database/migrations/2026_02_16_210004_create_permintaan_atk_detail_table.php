<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_atk_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('permintaan_id')->constrained('permintaan_atk')->onDelete('cascade');
            $table->foreignUuid('atk_id')->constrained('atk')->onDelete('cascade');
            $table->unsignedInteger('jumlah');
            $table->string('keterangan', 500)->nullable();
            $table->timestamps();

            $table->index('permintaan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_atk_detail');
    }
};
