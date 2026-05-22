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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel customers
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            // Foreign Key ke tabel services
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Status sesuai catatan modul: active, inactive, trial, isolir, dismantle
            $table->string('status')->default('trial');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
