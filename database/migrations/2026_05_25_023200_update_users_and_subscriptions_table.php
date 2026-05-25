<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // TAMBAHKAN INI: Ubah status di tabel services dari integer ke enum
        Schema::table('services', function (Blueprint $table) {
            // Kita gunakan change() untuk merubah tipe data kolom yang sudah ada
            $table->enum('status', ['active', 'inactive'])->default('active')->change();
        });

        // Update Tabel Users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'staff', 'customer'])->default('customer')->after('email');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
        });

        // Update Tabel Customers (Penghubung ke User)
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->after('id');
        });

        // Update Tabel Subscriptions (Sync Status Figma)
        // Catatan: Gunakan raw query jika enum lama sudah ada
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('active', 'deactivate', 'trial', 'isolir', 'dismantle') DEFAULT 'trial'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
