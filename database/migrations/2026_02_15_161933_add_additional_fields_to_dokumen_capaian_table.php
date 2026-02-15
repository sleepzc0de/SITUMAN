<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen_capaian', function (Blueprint $table) {
            // Ubah file_path menjadi nullable karena akan ada multiple files
            $table->text('file_path')->nullable()->change();

            // Tambah kolom untuk multiple files (JSON array)
            $table->json('files')->nullable()->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('dokumen_capaian', function (Blueprint $table) {
            $table->dropColumn('files');
        });
    }
};
