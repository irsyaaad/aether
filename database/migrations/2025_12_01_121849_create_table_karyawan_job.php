<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('karyawan_job', function (Blueprint $table) {
            $table->id();

            // Relasi ke karyawan
            $table->unsignedBigInteger('karyawan_id');

            // Detail pekerjaan
            $table->string('jabatan')->nullable();
            $table->string('divisi')->nullable();
            $table->string('lokasi_kerja')->nullable();
            $table->string('status_kerja')->nullable(); // Tetap/Kontrak/Harian
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('atasan_langsung')->nullable();
            $table->string('shift_kerja')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });

        // DB::table('karyawan_job')->insert([
        //     [
        //         'karyawan_id' => 1,
        //         'jabatan' => 'Staff Administrasi',
        //         'divisi' => 'Operasional',
        //         'lokasi_kerja' => 'Surabaya',
        //         'status_kerja' => 'Tetap',
        //         'tanggal_mulai' => '2021-01-10',
        //         'tanggal_selesai' => null,
        //         'atasan_langsung' => 'Manager Operasional',
        //         'shift_kerja' => 'Non-Shift',
        //         'catatan' => 'Karyawan menunjukkan performa baik.',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        //     [
        //         'karyawan_id' => 2,
        //         'jabatan' => 'Customer Service',
        //         'divisi' => 'Layanan Pelanggan',
        //         'lokasi_kerja' => 'Gresik',
        //         'status_kerja' => 'Kontrak',
        //         'tanggal_mulai' => '2022-06-01',
        //         'tanggal_selesai' => null,
        //         'atasan_langsung' => 'Supervisor CS',
        //         'shift_kerja' => 'Shift Pagi',
        //         'catatan' => 'Rajin dan disiplin.',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        // ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan_job');
    }
};
