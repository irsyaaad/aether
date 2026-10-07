<?php

namespace Modules\Task\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Karyawan;
use Modules\Master\Entities\Produk;
use Modules\Master\Entities\Toko;
use Modules\Task\Entities\Task;
use Modules\Task\Entities\TaskAssignee;
use Modules\Task\Entities\TaskCategory;
use Modules\Task\Entities\TaskPriority;
use Modules\Task\Entities\TaskSubmission;
use Modules\Task\Services\TaskNumberService;
use Modules\Task\Services\TaskRecurringService;
use Modules\Task\Services\TaskStatusService;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan kolom toko_id dan produk_id di task_data_entries tersedia
        if (Schema::hasTable('task_data_entries')) {
            if (!Schema::hasColumn('task_data_entries', 'toko_id')) {
                try {
                    Schema::table('task_data_entries', function (\Illuminate\Database\Schema\Blueprint $table) {
                        if (!Schema::hasColumn('task_data_entries', 'toko_id')) {
                            $table->unsignedBigInteger('toko_id')->nullable()->after('employee_id');
                        }
                        if (!Schema::hasColumn('task_data_entries', 'produk_id')) {
                            $table->unsignedBigInteger('produk_id')->nullable()->after('toko_id');
                        }
                    });
                } catch (\Throwable $e) {
                    // Abaikan jika sudah ada
                }
            }
        }

        $company = Company::firstOrCreate(
            ['code' => 'PAP'],
            [
                'name'      => 'PT Putera Adhi Perkasa',
                'email'     => 'admin@paphardware.com',
                'timezone'  => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );

        // 2. Ensure default priorities exist
        $priorityHigh = TaskPriority::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'HIGH'],
            ['name' => 'High', 'level' => 3, 'description' => 'Prioritas tinggi', 'is_active' => true]
        );
        $priorityMed = TaskPriority::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'MEDIUM'],
            ['name' => 'Medium', 'level' => 2, 'description' => 'Prioritas normal', 'is_active' => true]
        );
        $priorityLow = TaskPriority::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'LOW'],
            ['name' => 'Low', 'level' => 1, 'description' => 'Prioritas rendah', 'is_active' => true]
        );

        // 3. Ensure default categories exist
        $categoryOps = TaskCategory::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'OPS'],
            ['name' => 'Operasional', 'description' => 'Tugas operasional kantor dan toko', 'is_active' => true]
        );
        $categoryGen = TaskCategory::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'GEN'],
            ['name' => 'General', 'description' => 'Tugas umum operasional', 'is_active' => true]
        );
        $categoryMkt = TaskCategory::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'MKT'],
            ['name' => 'Marketing & Sosmed', 'description' => 'Tugas pemasaran, promosi, dan konten media sosial', 'is_active' => true]
        );
        $categoryCs = TaskCategory::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'CS'],
            ['name' => 'Customer Service', 'description' => 'Tugas penanganan chat, komplain, dan layanan pelanggan', 'is_active' => true]
        );
        $categoryAdm = TaskCategory::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'ADM'],
            ['name' => 'Administrasi', 'description' => 'Tugas administrasi, rekap data, dan pengarsipan', 'is_active' => true]
        );

        // Get admin user for creator
        $adminUserId = DB::table('users')->value('id_user') ?: 1;

        // 4. Ambil ID toko dan produk untuk konfigurasi template master task
        $allTokoIds = Toko::where('is_active', true)->pluck('id')->toArray();
        $allProdukIds = Produk::where('is_active', true)->pluck('id')->toArray();

        // Sample subsets for tasks with toko and product scoping
        $matrixTokoIds = !empty($allTokoIds) ? array_slice($allTokoIds, 0, 15) : [];
        $matrixProdukIds = !empty($allProdukIds) ? array_slice($allProdukIds, 0, 25) : [];
        $sampleTokoIds = !empty($allTokoIds) ? array_slice($allTokoIds, 0, 10) : null;
        $sampleProdukIds = !empty($allProdukIds) ? array_slice($allProdukIds, 0, 15) : null;

        // 5. 15 Template Master Task Lengkap (Murni Master Template)
        $taskTemplates = [
            [
                'title'       => 'Update Stock Kosong Toko & Marketplace',
                'description' => 'Cek ketersediaan fisik stok toko dan lakukan update stok kosong pada sistem marketplace.',
                'task_type'   => TaskRecurringService::TYPE_HARIAN,
                'output_type' => 'stock_update',
                'category_id' => $categoryOps->id,
                'priority_id' => $priorityHigh->id,
                'tokos'       => $matrixTokoIds,
                'produks'     => $matrixProdukIds,
            ],
            [
                'title'        => 'Rekap Pesanan Batal Harian',
                'description'  => 'Catat seluruh pesanan yang dibatalkan oleh pembeli beserta alasan pembatalannya.',
                'task_type'    => TaskRecurringService::TYPE_HARIAN,
                'output_type'  => 'data_list',
                'list_columns' => ['No. Pesanan', 'Toko / Channel', 'Nama Produk', 'Alasan Batal', 'Tindak Lanjut Customer'],
                'category_id'  => $categoryOps->id,
                'priority_id'  => $priorityMed->id,
                'tokos'        => $sampleTokoIds,
                'produks'      => $sampleProdukIds,
            ],
            [
                'title'        => 'Daftar Pesanan Terkendala & Komplain',
                'description'  => 'Monitoring status pesanan berkendala pengiriman dan keluhan pembeli.',
                'task_type'    => TaskRecurringService::TYPE_HARIAN,
                'output_type'  => 'data_list',
                'list_columns' => ['No. Pesanan', 'Toko', 'Nama Produk', 'Kurir / No. Resi', 'Kendala (Hilang/Rusak/Retur)', 'Status Penanganan'],
                'category_id'  => $categoryOps->id,
                'priority_id'  => $priorityHigh->id,
                'tokos'        => $sampleTokoIds,
                'produks'      => $sampleProdukIds,
            ],
            [
                'title'         => 'Target Pertumbuhan Customer Baru',
                'description'   => 'Pencapaian jumlah penambahan customer baru melalui program promosi toko.',
                'task_type'     => TaskRecurringService::TYPE_BULANAN,
                'output_type'   => 'target',
                'target_type'   => 'number',
                'target_unit'   => 'Customer Baru',
                'target_amount' => 50,
                'category_id'   => $categoryGen->id,
                'priority_id'   => $priorityMed->id,
            ],
            [
                'title'        => 'Rekap Daftar Pesanan Perhari',
                'description'  => 'Rekap harian total pesanan yang masuk dan siap dikirimkan untuk seluruh cabang.',
                'task_type'    => TaskRecurringService::TYPE_HARIAN,
                'output_type'  => 'data_list',
                'list_columns' => ['Toko / Cabang', 'Total Pesanan Masuk', 'Total Omset (Rp)', 'Pesanan Siap Kirim', 'Catatan'],
                'category_id'  => $categoryAdm->id,
                'priority_id'  => $priorityMed->id,
                'tokos'        => $sampleTokoIds,
            ],
            [
                'title'         => 'Layanan Chat Customer Per Hari',
                'description'   => 'Target respon dan penanganan chat customer toko online dengan ramah dan cepat.',
                'task_type'     => TaskRecurringService::TYPE_HARIAN,
                'output_type'   => 'target',
                'target_type'   => 'number',
                'target_unit'   => 'Chat Terlayani',
                'target_amount' => 100,
                'category_id'   => $categoryCs->id,
                'priority_id'   => $priorityMed->id,
            ],
            [
                'title'        => 'Jadwal & Laporan Posting Facebook',
                'description'  => 'Publikasi postingan produk dan konten edukasi hardware di halaman Facebook.',
                'task_type'    => TaskRecurringService::TYPE_HARIAN,
                'output_type'  => 'data_list',
                'list_columns' => ['Akun / Halaman Facebook', 'Topik / Judul Konten', 'Link Postingan', 'Jam Tayang', 'Status'],
                'category_id'  => $categoryMkt->id,
                'priority_id'  => $priorityMed->id,
            ],
            [
                'title'        => 'Pengumpulan Data Scraping Nomor WhatsApp',
                'description'  => 'Scraping nomor kontak potensial pembeli atau reseller dari forum/grup terkait.',
                'task_type'    => TaskRecurringService::TYPE_INSIDENTIL,
                'output_type'  => 'data_list',
                'list_columns' => ['Sumber / Nama Grup', 'Nama Kontak', 'Nomor WhatsApp', 'Status Validitas Kontak'],
                'category_id'  => $categoryMkt->id,
                'priority_id'  => $priorityMed->id,
            ],
            [
                'title'        => 'Penulisan & Publikasi Artikel Website',
                'description'  => 'Membuat artikel SEO bermanfaat mengenai produk hardware dan perkakas rumah tangga.',
                'task_type'    => TaskRecurringService::TYPE_BULANAN,
                'output_type'  => 'data_list',
                'list_columns' => ['Judul Artikel', 'Target Keyword (SEO)', 'Link Artikel Website', 'Jumlah Kata', 'Status Indeks'],
                'category_id'  => $categoryMkt->id,
                'priority_id'  => $priorityMed->id,
            ],
            [
                'title'        => 'Posting Konten WhatsApp Story & Instagram',
                'description'  => 'Update katalog promo berkala melalui status WhatsApp bisnis dan feed/story Instagram.',
                'task_type'    => TaskRecurringService::TYPE_HARIAN,
                'output_type'  => 'data_list',
                'list_columns' => ['Platform (WA Story / Feed IG / Reels)', 'Judul Konten / Promo', 'Link / Bukti Postingan', 'Jam Posting'],
                'category_id'  => $categoryMkt->id,
                'priority_id'  => $priorityMed->id,
            ],
            [
                'title'        => 'Pengaturan Event Flash Sale Marketplace',
                'description'  => 'Setting harga diskon dan kuota stok untuk promo kilat / flash sale marketplace.',
                'task_type'    => TaskRecurringService::TYPE_INSIDENTIL,
                'output_type'  => 'data_list',
                'list_columns' => ['Toko / Marketplace', 'Nama Event Flash Sale', 'Nama Produk', 'Harga Flash Sale', 'Kuota Stok'],
                'category_id'  => $categoryOps->id,
                'priority_id'  => $priorityHigh->id,
                'tokos'        => $sampleTokoIds,
                'produks'      => $sampleProdukIds,
            ],
            [
                'title'        => 'Daftar Campaign Marketplace yang Diikuti',
                'description'  => 'Pendaftaran keikutsertaan program mega campaign promo tanggal kembar atau gajian.',
                'task_type'    => TaskRecurringService::TYPE_BULANAN,
                'output_type'  => 'data_list',
                'list_columns' => ['Platform / Toko', 'Nama Campaign Promo', 'Periode Tanggal', 'Jumlah Produk Terdaftar', 'Status Approval'],
                'category_id'  => $categoryOps->id,
                'priority_id'  => $priorityMed->id,
                'tokos'        => $sampleTokoIds,
            ],
            [
                'title'        => 'Pengaturan & Monitoring Voucher Toko',
                'description'  => 'Pembuatan kode voucher diskon toko dan memonitor efektivitas penggunaan kupon.',
                'task_type'    => TaskRecurringService::TYPE_BULANAN,
                'output_type'  => 'data_list',
                'list_columns' => ['Toko / Channel', 'Kode / Nama Voucher', 'Diskon (Nominal / %)', 'Minimal Belanja', 'Kuota Voucher'],
                'category_id'  => $categoryOps->id,
                'priority_id'  => $priorityMed->id,
                'tokos'        => $sampleTokoIds,
            ],
            [
                'title'        => 'Pengaturan Pesan Broadcast Promosi',
                'description'  => 'Kirim pesan siaran promo berkala ke pelanggan setia dan follower toko.',
                'task_type'    => TaskRecurringService::TYPE_INSIDENTIL,
                'output_type'  => 'data_list',
                'list_columns' => ['Channel (WA / Shopee Chat)', 'Judul & Tema Pesan', 'Target Segmen Penerima', 'Waktu Kirim', 'Respon / Hasil'],
                'category_id'  => $categoryMkt->id,
                'priority_id'  => $priorityMed->id,
            ],
            [
                'title'       => 'Checklist Kebersihan & Kesiapan Operasional Toko',
                'description' => 'Pemeriksaan rutin kebersihan area etalase, sistem kasir, dan kesiapan packing harian.',
                'task_type'   => TaskRecurringService::TYPE_HARIAN,
                'output_type' => 'checklist',
                'category_id' => $categoryOps->id,
                'priority_id' => $priorityMed->id,
            ],
        ];

        // 6. Simpan atau perbarui master task templates
        $createdTasks = [];

        foreach ($taskTemplates as $tpl) {
            $isTemplate = in_array($tpl['task_type'], [TaskRecurringService::TYPE_HARIAN, TaskRecurringService::TYPE_BULANAN]);

            $taskData = [
                'company_id'          => $company->id,
                'title'               => $tpl['title'],
                'description'         => $tpl['description'],
                'category_id'         => $tpl['category_id'],
                'priority_id'         => $tpl['priority_id'],
                'task_type'           => $tpl['task_type'],
                'output_type'         => $tpl['output_type'],
                'target_type'         => $tpl['target_type'] ?? null,
                'target_unit'         => $tpl['target_unit'] ?? null,
                'target_amount'       => $tpl['target_amount'] ?? null,
                'target_count'        => $tpl['target_count'] ?? null,
                'list_columns'        => $tpl['list_columns'] ?? null,
                'selected_toko_ids'   => $tpl['tokos'] ?? null,
                'selected_produk_ids' => $tpl['produks'] ?? null,
                'is_template'         => $isTemplate,
                'parent_task_id'      => null,
                'recurring_date'      => null,
                'status'              => 'draft',
                'progress'            => 0,
                'start_date'          => Carbon::today(),
                'due_date'            => null,
                'created_by'          => $adminUserId,
            ];

            // Selalu cari berdasarkan title & company_id HANYA pada master template (whereNull parent_task_id)
            $task = Task::where('company_id', $company->id)
                ->whereNull('parent_task_id')
                ->where('title', $tpl['title'])
                ->first();

            if (!$task) {
                $taskData['task_number'] = TaskNumberService::generate($company->id, Carbon::today()->year);
                $task = Task::create($taskData);
            } else {
                $task->update($taskData);
            }

            $createdTasks[$tpl['title']] = $task;
        }

        // 7. Plotting tugas contoh ke karyawan jika data karyawan tersedia
        $empBudi = Karyawan::where('email', 'budi.santoso@paphardware.com')
            ->orWhere('employee_number', 'EMP-002')
            ->orWhere('name', 'like', '%Budi%')
            ->first();

        if ($empBudi) {
            // Plot tugas harian dan insidentil ke Budi
            $sampleTitlesForBudi = [
                'Update Stock Kosong Toko & Marketplace',
                'Rekap Pesanan Batal Harian',
                'Pengaturan Event Flash Sale Marketplace',
            ];

            foreach ($sampleTitlesForBudi as $tTitle) {
                if (isset($createdTasks[$tTitle])) {
                    $masterTask = $createdTasks[$tTitle];

                    $assignee = TaskAssignee::firstOrCreate(
                        [
                            'task_id'     => $masterTask->id,
                            'employee_id' => $empBudi->id,
                        ],
                        [
                            'assigned_at' => now(),
                            'assigned_by' => $adminUserId,
                            'start_date'  => Carbon::today(),
                            'due_date'    => Carbon::today(),
                            'status'      => TaskStatusService::STATUS_ASSIGNED,
                            'progress'    => 0,
                        ]
                    );

                    // Untuk tugas insidentil, buat task submission awal
                    if ($masterTask->task_type === TaskRecurringService::TYPE_INSIDENTIL) {
                        TaskSubmission::firstOrCreate(
                            [
                                'task_assignee_id' => $assignee->id,
                            ],
                            [
                                'task_id'     => $masterTask->id,
                                'employee_id' => $empBudi->id,
                                'created_by'  => $adminUserId,
                                'work_date'   => Carbon::today()->toDateString(),
                                'status'      => TaskStatusService::STATUS_ASSIGNED,
                                'progress'    => 0,
                            ]
                        );
                    }
                }
            }
        }

        // 8. Auto-generate instances untuk hari ini & bulan ini sesuai mekanisme recurring baru
        try {
            TaskRecurringService::generateAllDueRecurringTasks();
        } catch (\Throwable $e) {
            \Log::warning('TaskSeeder recurring generation warning: ' . $e->getMessage());
        }
    }
}
