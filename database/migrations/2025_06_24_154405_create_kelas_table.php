<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_kelas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas'); // Contoh: 10 Akuntansi 1
            $table->string('jurusan');    // Contoh: Akuntansi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};