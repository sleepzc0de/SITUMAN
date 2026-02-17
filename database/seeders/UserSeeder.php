<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\PasswordHashService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    private array $users = [
        [
            'nama'          => 'Auliya Putra Azhari',
            'nip'           => '199609102018011005',
            'email'         => 'auliyaputraazhari@kemenkeu.go.id',
            'email_pribadi' => 'auliyaputraazhari@gmail.com',
            'no_hp'         => '082211581510',
            'role'          => 'superadmin',
            'password'      => 'Admin@12345!',
        ],
        [
            'nama'          => 'Admin SiTUMAN',
            'nip'           => '199001011990011001',
            'email'         => 'admin@kemenkeu.go.id',
            'email_pribadi' => null,
            'no_hp'         => null,
            'role'          => 'admin',
            'password'      => 'Admin@12345!',
        ],
        [
            'nama'          => 'Eksekutif Demo',
            'nip'           => '199101011991011001',
            'email'         => 'eksekutif@kemenkeu.go.id',
            'email_pribadi' => null,
            'no_hp'         => null,
            'role'          => 'eksekutif',
            'password'      => 'User@12345!',
        ],
        [
            'nama'          => 'PIC Kepegawaian Demo',
            'nip'           => '199202021992021001',
            'email'         => 'picpegawai@kemenkeu.go.id',
            'email_pribadi' => null,
            'no_hp'         => null,
            'role'          => 'picpegawai',
            'password'      => 'User@12345!',
        ],
        [
            'nama'          => 'PIC Keuangan Demo',
            'nip'           => '199303031993031001',
            'email'         => 'pickeuangan@kemenkeu.go.id',
            'email_pribadi' => null,
            'no_hp'         => null,
            'role'          => 'pickeuangan',
            'password'      => 'User@12345!',
        ],
        [
            'nama'          => 'PIC Inventaris Demo',
            'nip'           => '199404041994041001',
            'email'         => 'picinventaris@kemenkeu.go.id',
            'email_pribadi' => null,
            'no_hp'         => null,
            'role'          => 'picinventaris',
            'password'      => 'User@12345!',
        ],
        [
            'nama'          => 'User Demo',
            'nip'           => '199502021995021001',
            'email'         => 'user@kemenkeu.go.id',
            'email_pribadi' => null,
            'no_hp'         => null,
            'role'          => 'user',
            'password'      => 'User@12345!',
        ],
        [
            'nama'          => 'User Tanpa Role',
            'nip'           => '199803031998031001',
            'email'         => 'norole@kemenkeu.go.id',
            'email_pribadi' => null,
            'no_hp'         => null,
            'role'          => null,
            'password'      => 'User@12345!',
        ],
    ];

    public function run(): void
    {
        $this->command->info('🌱 Seeding users dengan password salt...');
        $this->command->newLine();

        // Validasi constraint sebelum insert
        $this->ensureRoleConstraintUpdated();

        $created = 0;
        $skipped = 0;
        $failed  = 0;

        foreach ($this->users as $userData) {
            // Skip jika sudah ada
            $exists = User::where('nip', $userData['nip'])
                ->orWhere('email', $userData['email'])
                ->exists();

            if ($exists) {
                $this->command->warn("  ⚠  Skip : {$userData['nama']} — sudah ada.");
                $skipped++;
                continue;
            }

            try {
                // Generate salt & hash password
                $passwordResult = PasswordHashService::make($userData['password']);

                User::create([
                    'nama'          => $userData['nama'],
                    'nip'           => $userData['nip'],
                    'email'         => $userData['email'],
                    'email_pribadi' => $userData['email_pribadi'],
                    'no_hp'         => $userData['no_hp'],
                    'password'      => $passwordResult['hash'],
                    'password_salt' => $passwordResult['salt'],
                    'role'          => $userData['role'],
                ]);

                $roleLabel = $userData['role'] ?? 'Tanpa Role';
                $this->command->info("  ✅ {$userData['nama']} ({$roleLabel})");
                $created++;

            } catch (\Exception $e) {
                $this->command->error("  ❌ Gagal: {$userData['nama']} — {$e->getMessage()}");
                $failed++;
            }
        }

        $this->command->newLine();
        $this->command->info("🎉 Selesai! {$created} dibuat, {$skipped} dilewati, {$failed} gagal.");
        $this->command->newLine();

        // Tampilkan tabel kredensial
        if ($created > 0) {
            $this->command->table(
                ['Nama', 'Email', 'Role', 'Password'],
                collect($this->users)->map(fn ($u) => [
                    $u['nama'],
                    $u['email'],
                    $u['role'] ?? '-',
                    $u['password'],
                ])->toArray()
            );

            $this->command->newLine();
            $this->command->warn('⚠  Segera ganti password setelah login pertama!');
        }
    }

    // =========================================================
    // PRIVATE: Validasi constraint sebelum seeding
    // =========================================================

    private function ensureRoleConstraintUpdated(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver !== 'sqlsrv') {
            return;
        }

        // Cek apakah nilai 'eksekutif' sudah diizinkan
        $isUpdated = $this->checkRoleAllowed('eksekutif');

        if (!$isUpdated) {
            $this->command->warn('  ⚠  CHECK constraint role belum diupdate!');
            $this->command->warn('  ⚠  Menjalankan update constraint otomatis...');

            try {
                $this->updateSqlServerConstraint();
                $this->command->info('  ✅ Constraint berhasil diupdate.');
            } catch (\Exception $e) {
                $this->command->error('  ❌ Gagal update constraint: ' . $e->getMessage());
                $this->command->error('  ❌ Jalankan: php artisan migrate');
                throw $e;
            }
        } else {
            $this->command->info('  ✅ Constraint role sudah up-to-date.');
        }

        $this->command->newLine();
    }

    private function checkRoleAllowed(string $role): bool
    {
        try {
            // Coba cek dengan query ke sys.check_constraints
            $constraints = DB::select("
                SELECT con.definition
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
                if (str_contains($constraint->definition, $role)) {
                    return true;
                }
            }

            return false;

        } catch (\Exception $e) {
            // Jika tidak bisa cek, asumsikan perlu update
            return false;
        }
    }

    private function updateSqlServerConstraint(): void
    {
        // Hapus semua constraint lama pada kolom role
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
            DB::statement("ALTER TABLE users DROP CONSTRAINT [{$constraint->constraint_name}]");
        }

        // Buat constraint baru
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
    }
}
