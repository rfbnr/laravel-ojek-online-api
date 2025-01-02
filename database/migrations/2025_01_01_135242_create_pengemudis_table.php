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
        Schema::create('pengemudis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengemudi');
            $table->string('email_pengemudi');
            $table->string('phone_pengemudi');
            $table->string('merek_tipe_kendaraan');
            $table->string('plat_nomor');
            $table->enum('status_pengemudi', ['aktif', 'nonaktif', 'on-trip']);
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('tgl_registrasi');
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengemudis');
    }
};
