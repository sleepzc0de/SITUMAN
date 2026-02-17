<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique(); // slug: superadmin, admin, user, dst
            $table->string('display_name');   // Nama tampilan
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();        // slug: dashboard.view, kepegawaian.view
            $table->string('display_name');
            $table->string('module');                // dashboard, kepegawaian, anggaran, inventaris, users
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Pivot Role-Permission
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->uuid('role_id');
            $table->uuid('permission_id');
            $table->primary(['role_id', 'permission_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        // Pivot User-Role (user bisa punya satu role utama, simpan di users.role,
        // tapi kita tambahkan tabel ini untuk fleksibilitas multi-role ke depan)
        Schema::create('user_roles', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('role_id');
            $table->primary(['user_id', 'role_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
