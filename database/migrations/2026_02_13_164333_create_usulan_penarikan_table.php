<?php
// database/migrations/2026_02_13_164333_create_usulan_penarikan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usulan_penarikan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ro', 50);
            $table->string('sub_komponen', 255);
            $table->string('bulan', 20);
            $table->decimal('nilai_usulan', 20, 2);
            $table->text('keterangan')->nullable();
            $table->string('status', 20)->default('pending');
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Index
            $table->index(['status'], 'idx_status');
            $table->index(['bulan'], 'idx_bulan');
            $table->index(['ro'], 'idx_ro');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usulan_penarikan');
    }
};
