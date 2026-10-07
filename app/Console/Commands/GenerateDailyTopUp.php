<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Operasional\Entities\DailyTopUp;
use Modules\Keuangan\Entities\KasBankMasuk;
use Modules\Keuangan\Entities\KasBankKeluar;

class GenerateDailyTopUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily-top-up:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate transaksi harian dari Daily Top Up yang aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai generate transaksi Daily Top Up...');
        
        try {
            $today = date('Y-m-d');
            
            // Get active daily top ups for today
            $topUps = DailyTopUp::where('status', 'aktif')
                ->where('tanggal_dari', '<=', $today)
                ->where('tanggal_sampai', '>=', $today)
                ->get();

            if ($topUps->isEmpty()) {
                $this->warn('Tidak ada Daily Top Up aktif untuk hari ini.');
                return 0;
            }

            $this->info("Ditemukan {$topUps->count()} Daily Top Up aktif.");

            $generated = 0;
            $disabled = 0;
            $skipped = 0;

            DB::beginTransaction();

            foreach ($topUps as $topUp) {
                // Check if already generated today
                $tableName = $topUp->jenis_transaksi == 'masuk' ? 'kas_bank_masuk' : 'kas_bank_keluar';
                $exists = DB::table($tableName)
                    ->where('tanggal', $today)
                    ->where('keterangan', 'LIKE', '%Daily Top Up Auto%')
                    ->where('kas_bank_id', $topUp->rekening_tujuan_id)
                    ->exists();

                if ($exists) {
                    $this->line("⏭️  Transaksi untuk Top Up ID {$topUp->id} sudah ada, dilewati.");
                    $skipped++;
                    continue;
                }

                // Generate transaksi
                $keterangan = 'Daily Top Up Auto - ' . $topUp->rekeningAsal->nama . ' ke ' . $topUp->rekeningTujuan->nama;
                
                if ($topUp->jenis_transaksi == 'masuk') {
                    $kasBankMasuk = KasBankMasuk::create([
                        'uuid' => \Illuminate\Support\Str::uuid(),
                        'kode_kas_bank_masuk' => KasBankMasuk::generateNotaNumber(),
                        'tanggal' => $today,
                        'kas_bank_id' => $topUp->rekening_tujuan_id,
                        'nominal' => $topUp->nominal,
                        'keterangan' => $keterangan,
                        'created_by' => 1, // System user
                    ]);
                    
                    // Create detail
                    \Modules\Keuangan\Entities\KasBankMasukDetail::create([
                        'kas_bank_masuk_id' => $kasBankMasuk->id,
                        'ac_debet' => $topUp->rekening_tujuan_id,
                        'ac_kredit' => $topUp->rekening_asal_id,
                        'nominal' => $topUp->nominal,
                        'uraian' => $keterangan,
                    ]);
                } else {
                    $kasBankKeluar = KasBankKeluar::create([
                        'uuid' => \Illuminate\Support\Str::uuid(),
                        'kode_kas_bank_keluar' => KasBankKeluar::generateNotaNumber(),
                        'tanggal' => $today,
                        'kas_bank_id' => $topUp->rekening_asal_id,
                        'nominal' => $topUp->nominal,
                        'keterangan' => $keterangan,
                        'created_by' => 1, // System user
                    ]);
                    
                    // Create detail
                    \Modules\Keuangan\Entities\KasBankKeluarDetail::create([
                        'kas_bank_keluar_id' => $kasBankKeluar->id,
                        'ac_debet' => $topUp->rekening_tujuan_id,
                        'ac_kredit' => $topUp->rekening_asal_id,
                        'nominal' => $topUp->nominal,
                        'uraian' => $keterangan,
                    ]);
                }
                
                $this->line("✅ Transaksi untuk Top Up ID {$topUp->id} berhasil dibuat.");
                $generated++;

                // Auto-disable if past end date
                if ($topUp->tanggal_sampai < $today) {
                    $topUp->update(['status' => 'tidak aktif']);
                    $this->line("🔴 Top Up ID {$topUp->id} dinonaktifkan (melewati tanggal sampai).");
                    $disabled++;
                }
            }

            DB::commit();

            $this->newLine();
            $this->info("✨ Selesai!");
            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    ['Transaksi dibuat', $generated],
                    ['Transaksi dilewati', $skipped],
                    ['Top Up dinonaktifkan', $disabled],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Gagal generate transaksi: ' . $e->getMessage());
            return 1;
        }
    }
}
