<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->date('tmt_jabatan')->nullable()->after('jabatan')
                  ->comment('Tanggal Mulai Tugas jabatan saat ini');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn('tmt_jabatan');
        });
    }
};
