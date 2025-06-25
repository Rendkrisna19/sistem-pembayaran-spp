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
            Schema::create('pembayarans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // ID Bendahara yg verifikasi
                $table->date('tanggal_bayar');
                $table->string('bulan_dibayar', 20); // Contoh: "Januari 2024"
                $table->string('tahun_dibayar', 4);
                $table->decimal('jumlah_bayar', 10, 2);
                $table->string('bukti_pembayaran')->nullable(); // Path ke file gambar
                $table->enum('status', ['Menunggu Verifikasi', 'Lunas', 'Ditolak'])->default('Menunggu Verifikasi');
                $table->text('keterangan')->nullable(); // Catatan dari bendahara
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('pembayarans');
        }
    };
    