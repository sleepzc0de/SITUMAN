<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlsrv') {
            $this->updateSqlServer();
        } elseif ($driver === 'mysql') {
            $this->updateMysql();
        } elseif ($driver === 'pgsql') {
            $this->updatePostgres();
        } else {
            // SQLite: ubah jadi string biasa
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlsrv') {
            $this->revertSqlServer();
        } elseif ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE users
                MODIFY COLUMN role ENUM('superadmin','admin','user') NULL
            ");
        }
    }

    // =========================================================
    // SQL SERVER
    // =========================================================

    private function updateSqlServer(): void
    {
        // Cari dan hapus semua CHECK constraint pada kolom role
        $constraints = DB::select("
            SELECT con.name AS constraint_name
            FROM sys.check_constraints con
            INNER JOIN sys.columns col
                ON con.parent_object_id = col.object_id
                AND con.parent_column_id = col.column_id
            INNER JOIN sys.tables tbl
                ON con.parent_object_id = tbl.object_id
            WHERE tbl.name = 'users'
              AND col.name = 'role'
        ");

        foreach ($constraints as $constraint) {
            DB::statement("
                ALTER TABLE users
                DROP CONSTRAINT [{$constraint->constraint_name}]
            ");
            $this->logInfo("Dropped constraint: {$constraint->constraint_name}");
        }

        // Tambahkan CHECK constraint baru dengan semua role
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT CK_users_role
            CHECK (role IN (
                'superadmin',
                'admin',
                'user',
                'eksekutif',
                'picpegawai',
                'pickeuangan',
                'picinventaris'
            ) OR role IS NULL)
        ");

        $this->logInfo('CHECK constraint CK_users_role berhasil dibuat.');
    }

    private function revertSqlServer(): void
    {
        // Hapus constraint baru
        $exists = DB::select("
            SELECT name FROM sys.check_constraints
            WHERE name = 'CK_users_role'
        ");

        if (!empty($exists)) {
            DB::statement("ALTER TABLE users DROP CONSTRAINT CK_users_role");
        }

        // Kembalikan constraint lama
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT CK_users_role_old
            CHECK (role IN ('superadmin','admin','user') OR role IS NULL)
        ");
    }

    // =========================================================
    // MYSQL
    // =========================================================

    private function updateMysql(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM(
                'superadmin',
                'admin',
                'user',
                'eksekutif',
                'picpegawai',
                'pickeuangan',
                'picinventaris'
            ) NULL
        ");
    }

    // =========================================================
    // POSTGRESQL
    // =========================================================

    private function updatePostgres(): void
    {
        // Drop constraint lama jika ada
        DB::statement("
            ALTER TABLE users
            DROP CONSTRAINT IF EXISTS users_role_check
        ");

        // Tambah constraint baru
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT users_role_check
            CHECK (role IN (
                'superadmin',
                'admin',
                'user',
                'eksekutif',
                'picpegawai',
                'pickeuangan',
                'picinventaris'
            ))
        ");
    }

    // =========================================================
    // HELPER
    // =========================================================

    private function logInfo(string $message): void
    {
        if (app()->runningInConsole()) {
            echo "   INFO  {$message}" . PHP_EOL;
        }
    }
};
