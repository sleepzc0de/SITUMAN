<?php
// database/migrations/2026_03_11_000001_add_anggaran_relations.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah anggaran_id ke usulan_penarikan
        Schema::table('usulan_penarikan', function (Blueprint $table) {
            $table->uuid('anggaran_id')->nullable()->after('id');
            $table->foreign('anggaran_id')
                  ->references('id')
                  ->on('anggaran')
                  ->onDelete('set null');
            $table->index('anggaran_id');
        });

        // Tambah anggaran_id ke dokumen_capaian
        Schema::table('dokumen_capaian', function (Blueprint $table) {
            $table->uuid('anggaran_id')->nullable()->after('id');
            $table->foreign('anggaran_id')
                  ->references('id')
                  ->on('anggaran')
                  ->onDelete('set null');
            $table->index('anggaran_id');
        });

        // Tambah kolom tahun ke semua tabel anggaran untuk multi-tahun
        Schema::table('anggaran', function (Blueprint $table) {
            $table->year('tahun')->default(date('Y'))->after('ro');
            $table->index('tahun');
        });

        Schema::table('revisi_anggaran', function (Blueprint $table) {
            $table->string('status', 20)->default('approved')->after('user_id');
            $table->text('catatan_reviewer')->nullable()->after('status');
            $table->uuid('reviewed_by')->nullable()->after('catatan_reviewer');
        });
    }

    public function down(): void
    {
        Schema::table('usulan_penarikan', function (Blueprint $table) {
            $table->dropForeign(['anggaran_id']);
            $table->dropColumn('anggaran_id');
        });

        Schema::table('dokumen_capaian', function (Blueprint $table) {
            $table->dropForeign(['anggaran_id']);
            $table->dropColumn('anggaran_id');
        });

        Schema::table('anggaran', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });
    }
};
