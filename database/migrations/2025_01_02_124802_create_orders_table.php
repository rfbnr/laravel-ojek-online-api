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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users');
            $table->foreignId('id_pengemudi')->constrained('pengemudis');
            $table->string('longitute_jemput');
            $table->string('latitude_jemput');
            $table->string('longitute_tujuan');
            $table->string('latitude_tujuan');
            $table->timestamp('waktu_order');
            $table->timestamp('waktu_terima_order')->nullable();
            $table->timestamp('waktu_jemput')->nullable();
            $table->timestamp('waktu_sampai')->nullable();
            $table->integer('total_harga');
            $table->integer('harga_bersih');
            $table->enum('status_perjalanan', ['menunggu', 'diperjalanan', 'dijemput', 'selesai']);
            $table->string('id_metode_pembayaran')->constrained('metode_pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
