<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Set panjang kolom eksplisit pada tabel pegawai
     * sebagai pertahanan lapisan database terhadap input berlebih.
     *
     * Catatan SQL Server:
     * - Kolom dengan unique/index harus di-drop indexnya dulu sebelum di-alter
     * - Membutuhkan doctrine/dbal: composer require doctrine/dbal
     */
    public function up(): void
    {
        // ── STEP 1: Drop indexes yang dependent ke kolom yang akan diubah
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropUnique('pegawai_nip_unique');
            $table->dropIndex(['bagian', 'subbagian']);
            $table->dropIndex(['status']);
        });

        // ── STEP 2: Alter kolom dengan panjang baru
        Schema::table('pegawai', function (Blueprint $table) {
            // Identitas
            $table->string('nama', 100)->change();
            $table->string('nama_gelar', 150)->nullable()->change();
            $table->string('nip', 25)->change();
            $table->string('pangkat', 50)->nullable()->change();
            $table->string('pendidikan', 20)->nullable()->change();
            $table->string('email_kemenkeu', 100)->nullable()->change();
            $table->string('email_pribadi', 100)->nullable()->change();
            $table->string('no_hp', 20)->nullable()->change();

            // Jabatan & Unit
            $table->string('jabatan', 100)->nullable()->change();
            $table->string('jenis_jabatan', 30)->nullable()->change();
            $table->string('nama_jabatan', 200)->nullable()->change();
            $table->string('eselon', 20)->nullable()->change();
            $table->string('jenis_pegawai', 20)->nullable()->change();
            $table->string('status', 20)->nullable()->change();
            $table->string('lokasi', 100)->nullable()->change();
            $table->string('bagian', 150)->nullable()->change();
            $table->string('subbagian', 150)->nullable()->change();

            // Pendidikan
            $table->string('jurusan_s1', 150)->nullable()->change();
            $table->string('jurusan_s2', 150)->nullable()->change();
            $table->string('jurusan_s3', 150)->nullable()->change();

            // KP
            $table->string('bulan_lahir', 20)->nullable()->change();
            $table->string('proyeksi_kp_1', 50)->nullable()->change();
            $table->string('proyeksi_kp_2', 50)->nullable()->change();
        });

        // ── STEP 3: Re-create indexes
        Schema::table('pegawai', function (Blueprint $table) {
            $table->unique('nip', 'pegawai_nip_unique');
            $table->index(['bagian', 'subbagian']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        // Drop indexes dulu
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropUnique('pegawai_nip_unique');
            $table->dropIndex(['bagian', 'subbagian']);
            $table->dropIndex(['status']);
        });

        // Kembalikan ke default Laravel string (255)
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('nama')->change();
            $table->string('nama_gelar')->nullable()->change();
            $table->string('nip')->change();
            $table->string('pangkat')->nullable()->change();
            $table->string('pendidikan')->nullable()->change();
            $table->string('email_kemenkeu')->nullable()->change();
            $table->string('email_pribadi')->nullable()->change();
            $table->string('no_hp')->nullable()->change();
            $table->string('jabatan')->nullable()->change();
            $table->string('jenis_jabatan')->nullable()->change();
            $table->string('nama_jabatan')->nullable()->change();
            $table->string('eselon')->nullable()->change();
            $table->string('jenis_pegawai')->nullable()->change();
            $table->string('status')->nullable()->change();
            $table->string('lokasi')->nullable()->change();
            $table->string('bagian')->nullable()->change();
            $table->string('subbagian')->nullable()->change();
            $table->string('jurusan_s1')->nullable()->change();
            $table->string('jurusan_s2')->nullable()->change();
            $table->string('jurusan_s3')->nullable()->change();
            $table->string('bulan_lahir')->nullable()->change();
            $table->string('proyeksi_kp_1')->nullable()->change();
            $table->string('proyeksi_kp_2')->nullable()->change();
        });

        // Re-create indexes
        Schema::table('pegawai', function (Blueprint $table) {
            $table->unique('nip', 'pegawai_nip_unique');
            $table->index(['bagian', 'subbagian']);
            $table->index('status');
        });
    }
};
