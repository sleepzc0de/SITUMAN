<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggaran', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan', 50);
            $table->string('kro', 50);
            $table->string('ro', 50);
            $table->string('kode_subkomponen', 50)->nullable();
            $table->string('kode_akun', 50)->nullable();
            $table->text('program_kegiatan');
            $table->string('pic', 100);
            $table->decimal('pagu_anggaran', 20, 2);
            $table->string('referensi', 100);
            $table->string('referensi2', 100);
            $table->string('ref_output', 100);
            $table->integer('len');

            $table->decimal('januari', 20, 2)->default(0);
            $table->decimal('februari', 20, 2)->default(0);
            $table->decimal('maret', 20, 2)->default(0);
            $table->decimal('april', 20, 2)->default(0);
            $table->decimal('mei', 20, 2)->default(0);
            $table->decimal('juni', 20, 2)->default(0);
            $table->decimal('juli', 20, 2)->default(0);
            $table->decimal('agustus', 20, 2)->default(0);
            $table->decimal('september', 20, 2)->default(0);
            $table->decimal('oktober', 20, 2)->default(0);
            $table->decimal('november', 20, 2)->default(0);
            $table->decimal('desember', 20, 2)->default(0);

            $table->decimal('tagihan_outstanding', 20, 2)->default(0);
            $table->decimal('total_penyerapan', 20, 2)->default(0);
            $table->decimal('sisa', 20, 2)->default(0);

            $table->timestamps();

            $table->index(['kegiatan', 'kro', 'ro', 'kode_akun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggaran');
    }
};
