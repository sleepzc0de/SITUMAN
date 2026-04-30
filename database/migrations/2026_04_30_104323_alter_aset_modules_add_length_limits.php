<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $lenFn = DB::getDriverName() === 'sqlsrv' ? 'LEN' : 'LENGTH';

        // Truncate data yang melebihi batas (jika ada) sebelum alter
        DB::statement("UPDATE kategori_aset SET deskripsi = LEFT(deskripsi, 2000) WHERE {$lenFn}(deskripsi) > 2000");
        DB::statement("UPDATE aset_end_user SET deskripsi = LEFT(deskripsi, 2000) WHERE {$lenFn}(deskripsi) > 2000");
        DB::statement("UPDATE aset_end_user SET catatan = LEFT(catatan, 2000) WHERE {$lenFn}(catatan) > 2000");
        DB::statement("UPDATE riwayat_aset SET keterangan = LEFT(keterangan, 1000) WHERE {$lenFn}(keterangan) > 1000");

        if (DB::getDriverName() === 'sqlsrv') {
            // ============================================
            // KATEGORI ASET
            // ============================================
            DB::statement("ALTER TABLE kategori_aset ALTER COLUMN nama NVARCHAR(100) NOT NULL");
            DB::statement("ALTER TABLE kategori_aset ALTER COLUMN deskripsi NVARCHAR(2000) NULL");

            // ============================================
            // ASET END USER
            // ============================================
            // PERBAIKAN: Drop unique index dulu sebelum alter kolom kode_aset
            DB::statement("IF EXISTS (SELECT * FROM sys.indexes WHERE name = 'aset_end_user_kode_aset_unique' AND object_id = OBJECT_ID('aset_end_user'))
                          DROP INDEX aset_end_user_kode_aset_unique ON aset_end_user");

            // Sekarang aman untuk alter kolom
            DB::statement("ALTER TABLE aset_end_user ALTER COLUMN kode_aset NVARCHAR(50) NOT NULL");
            DB::statement("ALTER TABLE aset_end_user ALTER COLUMN nama_aset NVARCHAR(255) NOT NULL");
            DB::statement("ALTER TABLE aset_end_user ALTER COLUMN deskripsi NVARCHAR(2000) NULL");
            DB::statement("ALTER TABLE aset_end_user ALTER COLUMN merek NVARCHAR(100) NULL");
            DB::statement("ALTER TABLE aset_end_user ALTER COLUMN tipe NVARCHAR(100) NULL");
            DB::statement("ALTER TABLE aset_end_user ALTER COLUMN nomor_seri NVARCHAR(100) NULL");
            DB::statement("ALTER TABLE aset_end_user ALTER COLUMN catatan NVARCHAR(2000) NULL");

            // Buat ulang unique index
            DB::statement("CREATE UNIQUE INDEX aset_end_user_kode_aset_unique ON aset_end_user (kode_aset)");

            // ============================================
            // RIWAYAT ASET
            // ============================================
            DB::statement("ALTER TABLE riwayat_aset ALTER COLUMN keterangan NVARCHAR(1000) NULL");
        } else {
            Schema::table('kategori_aset', function (Blueprint $table) {
                $table->string('nama', 100)->change();
                $table->string('deskripsi', 2000)->nullable()->change();
            });

            Schema::table('aset_end_user', function (Blueprint $table) {
                $table->string('kode_aset', 50)->change();
                $table->string('nama_aset', 255)->change();
                $table->string('deskripsi', 2000)->nullable()->change();
                $table->string('merek', 100)->nullable()->change();
                $table->string('tipe', 100)->nullable()->change();
                $table->string('nomor_seri', 100)->nullable()->change();
                $table->string('catatan', 2000)->nullable()->change();
            });

            Schema::table('riwayat_aset', function (Blueprint $table) {
                $table->string('keterangan', 1000)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            // Drop index dulu
            DB::statement("IF EXISTS (SELECT * FROM sys.indexes WHERE name = 'aset_end_user_kode_aset_unique' AND object_id = OBJECT_ID('aset_end_user'))
                          DROP INDEX aset_end_user_kode_aset_unique ON aset_end_user");

            // Kembalikan kolom ke ukuran besar
            DB::statement("ALTER TABLE kategori_aset ALTER COLUMN deskripsi NVARCHAR(MAX) NULL");
            DB::statement("ALTER TABLE aset_end_user ALTER COLUMN kode_aset NVARCHAR(255) NOT NULL");
            DB::statement("ALTER TABLE aset_end_user ALTER COLUMN deskripsi NVARCHAR(MAX) NULL");
            DB::statement("ALTER TABLE aset_end_user ALTER COLUMN catatan NVARCHAR(MAX) NULL");
            DB::statement("ALTER TABLE riwayat_aset ALTER COLUMN keterangan NVARCHAR(MAX) NULL");

            // Buat ulang unique index
            DB::statement("CREATE UNIQUE INDEX aset_end_user_kode_aset_unique ON aset_end_user (kode_aset)");
        } else {
            Schema::table('kategori_aset', fn($t) => $t->text('deskripsi')->nullable()->change());
            Schema::table('aset_end_user', function ($t) {
                $t->text('deskripsi')->nullable()->change();
                $t->text('catatan')->nullable()->change();
            });
            Schema::table('riwayat_aset', fn($t) => $t->text('keterangan')->nullable()->change());
        }
    }
};
