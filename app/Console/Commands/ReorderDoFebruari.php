<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Operasional\Entities\DoModel;

class ReorderDoFebruari extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'do:reorder-februari {year=2026}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengurutkan ulang nomor DO di bulan Februari';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $year = $this->argument('year');
        $month = 2; // Februari
        
        $this->info("Memulai pengurutan ulang nomor DO untuk Februari {$year}...");
        
        try {
            DB::beginTransaction();
            
            // Ambil semua DO di bulan Februari berdasarkan tanggal_do, urutkan berdasarkan tanggal_do
            $dos = DoModel::withTrashed()
                ->whereYear('tanggal_do', $year)
                ->whereMonth('tanggal_do', $month)
                ->orderBy('tanggal_do', 'asc')
                ->orderBy('id', 'asc')
                ->get();
            
            if ($dos->isEmpty()) {
                $this->warn("Tidak ada data DO di bulan Februari {$year}");
                return 0;
            }
            
            $this->info("Ditemukan {$dos->count()} DO di bulan Februari {$year}");
            
            // Format prefix untuk Februari
            $yearMonth = substr($year, -2) . '02'; // Format: 2602 untuk Februari 2026
            $prefix = 'DO/SDA/' . $yearMonth . '/';
            
            $counter = 1;
            $updated = 0;
            
            $this->output->progressStart($dos->count());
            
            foreach ($dos as $do) {
                $oldKodeDo = $do->kode_do;
                $newKodeDo = $prefix . str_pad($counter, 3, '0', STR_PAD_LEFT);
                
                // Update kode_do
                $do->kode_do = $newKodeDo;
                $do->save();
                
                $this->line("  [{$counter}] {$oldKodeDo} → {$newKodeDo} (Tanggal DO: {$do->tanggal_do})");
                
                $counter++;
                $updated++;
                $this->output->progressAdvance();
            }
            
            $this->output->progressFinish();
            
            DB::commit();
            
            $this->info("\n✓ Berhasil mengurutkan ulang {$updated} nomor DO di bulan Februari {$year}");
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("✗ Terjadi kesalahan: " . $e->getMessage());
            return 1;
        }
    }
}
