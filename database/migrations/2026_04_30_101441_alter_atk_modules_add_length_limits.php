<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Truncate data yang melebihi batas (jika ada) sebelum alter
        DB::statement("UPDATE atk SET deskripsi = LEFT(deskripsi, 2000) WHERE LENGTH(deskripsi) > 2000");
        DB::statement("UPDATE kategori_atk SET deskripsi = LEFT(deskripsi, 1000) WHERE LENGTH(deskripsi) > 1000");
        DB::statement("UPDATE permintaan_atk SET keterangan = LEFT(keterangan, 1000) WHERE LENGTH(keterangan) > 1000");
        DB::statement("UPDATE permintaan_atk SET alasan_penolakan = LEFT(alasan_penolakan, 500) WHERE LENGTH(alasan_penolakan) > 500");
        DB::statement("UPDATE permintaan_atk_detail SET keterangan = LEFT(keterangan, 500) WHERE LENGTH(keterangan) > 500");

        Schema::table('atk', function (Blueprint $table) {
            $table->string('kode_atk', 50)->unique()->change();
            $table->string('nama', 255)->change();
            $table->string('deskripsi', 2000)->nullable()->change();
            $table->string('satuan', 20)->change();
        });

        Schema::table('kategori_atk', function (Blueprint $table) {
            $table->string('nama', 100)->change();
            $table->string('deskripsi', 1000)->nullable()->change();
        });

        Schema::table('permintaan_atk', function (Blueprint $table) {
            $table->string('nomor_permintaan', 50)->unique()->change();
            $table->string('keterangan', 1000)->nullable()->change();
            $table->string('alasan_penolakan', 500)->nullable()->change();
        });

        Schema::table('permintaan_atk_detail', function (Blueprint $table) {
            $table->string('keterangan', 500)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('atk', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->change();
        });

        Schema::table('kategori_atk', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->change();
        });

        Schema::table('permintaan_atk', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->change();
            $table->text('alasan_penolakan')->nullable()->change();
        });

        Schema::table('permintaan_atk_detail', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->change();
        });
    }
};
