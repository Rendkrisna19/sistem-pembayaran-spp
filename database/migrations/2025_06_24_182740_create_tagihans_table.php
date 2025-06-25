    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('tagihans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
                $table->foreignId('pembayaran_id')->nullable()->constrained('pembayarans')->onDelete('set null');
                $table->string('deskripsi'); // Contoh: "SPP Bulan Juli 2024"
                $table->decimal('jumlah_tagihan', 10, 2);
                $table->enum('status', ['Belum Lunas', 'Menunggu Verifikasi', 'Lunas', 'Ditolak'])->default('Belum Lunas');
                $table->date('tanggal_tagihan');
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('tagihans');
        }
    };
    