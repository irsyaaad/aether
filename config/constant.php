<?php

return [

    'permissions' => [
        'DASHBOARD'   => [
            'dashboard'  => [
                'label'   => 'Dashboard',
                'actions' => [
                    'operasional' => 'Operasional',
                    'keuangan'    => 'Keuangan',
                ],
            ],
            'perusahaan' => [
                'label'   => 'Perusahaan',
                'actions' => [
                    'index'  => 'Lihat Perusahaan',
                    'create' => 'Buat Perusahaan',
                    'update' => 'Ubah Perusahaan',
                    'delete' => 'Hapus Perusahaan',
                ],
            ],
            'list-tugas' => [
                'label'   => 'List Tugas (Kanban)',
                'actions' => [
                    'index'  => 'Lihat Board List Tugas',
                    'create' => 'Buat Board/Kolom/Kartu',
                    'update' => 'Ubah Board/Kolom/Kartu',
                    'delete' => 'Hapus Board/Kolom/Kartu',
                ],
            ],
        ],
        'SDM'         => [
            'user'                 => [
                'label'   => 'User',
                'actions' => [
                    'index'  => 'Lihat User',
                    'create' => 'Buat User',
                    'update' => 'Ubah User',
                    'delete' => 'Hapus User',
                ],
            ],
            'role'                 => [
                'label'   => 'Role',
                'actions' => [
                    'index'  => 'Lihat Role',
                    'create' => 'Buat Role',
                    'update' => 'Ubah Role',
                    'delete' => 'Hapus Role',
                ],
            ],
            'iklan'                => [
                'label'   => 'Iklan',
                'actions' => [
                    'index'  => 'Lihat Iklan',
                    'create' => 'Buat Iklan',
                    'update' => 'Ubah Iklan',
                    'delete' => 'Hapus Iklan',
                ],
            ],
            'kandidat'             => [
                'label'   => 'Kandidat',
                'actions' => [
                    'index'  => 'Lihat Kandidat',
                    'create' => 'Buat Kandidat',
                    'update' => 'Ubah Kandidat',
                    'delete' => 'Hapus Kandidat',
                ],
            ],
            'karyawan'             => [
                'label'   => 'Karyawan',
                'actions' => [
                    'index'  => 'Lihat Karyawan',
                    'create' => 'Buat Karyawan',
                    'update' => 'Ubah Karyawan',
                    'delete' => 'Hapus Karyawan',
                ],
            ],
            'absensi'              => [
                'label'   => 'Absensi',
                'actions' => [
                    'index'  => 'Lihat Absensi',
                    'create' => 'Buat Absensi',
                    'update' => 'Ubah Absensi',
                    'delete' => 'Hapus Absensi',
                ],
            ],
            'employee-activities'  => [
                'label'   => 'Aktivitas Karyawan',
                'actions' => [
                    'index'  => 'Lihat Aktivitas Karyawan',
                    'create' => 'Buat Aktivitas Karyawan',
                    'update' => 'Ubah Aktivitas Karyawan',
                    'delete' => 'Hapus Aktivitas Karyawan',
                ],
            ],

            'izin'                 => [
                'label'   => 'Izin',
                'actions' => [
                    'index'  => 'Lihat Izin',
                    'create' => 'Buat Izin',
                    'update' => 'Ubah Izin',
                    'delete' => 'Hapus Izin',
                ],
            ],

            'daily-report'         => [
                'label'   => 'Lap. Harian Kry',
                'actions' => [
                    'index'  => 'Lihat Lap. Harian',
                    'create' => 'Buat Lap. Harian',
                    'update' => 'Ubah Lap. Harian',
                    'delete' => 'Hapus Lap. Harian',
                ],
            ],

            'daily-report-summary' => [
                'label'   => 'Lap. Harian Kry (Dirut)',
                'actions' => [
                    'index' => 'Lihat Lap. Harian',
                ],
            ],

            'kontrak-karyawan'     => [
                'label'   => 'Kontrak Karyawan',
                'actions' => [
                    'index'  => 'Lihat Kontrak Karyawan',
                    'create' => 'Buat Kontrak Karyawan',
                    'update' => 'Ubah Kontrak Karyawan',
                    'delete' => 'Hapus Kontrak Karyawan',
                ],
            ],

            'pkwt'                 => [
                'label'   => 'PKWT Karyawan',
                'actions' => [
                    'index'  => 'Lihat PKWT Karyawan',
                    'create' => 'Buat PKWT Karyawan',
                    'update' => 'Ubah PKWT Karyawan',
                    'delete' => 'Hapus PKWT Karyawan',
                ],
            ],

            'payroll-period'       => [
                'label'   => 'Payroll Period',
                'actions' => [
                    'index'  => 'Lihat Payroll Period',
                    'create' => 'Buat Payroll Period',
                    'update' => 'Ubah Payroll Period',
                    'delete' => 'Hapus Payroll Period',
                ],
            ],

            'payroll-component'    => [
                'label'   => 'Payroll Component',
                'actions' => [
                    'index'  => 'Lihat Payroll Component',
                    'create' => 'Buat Payroll Component',
                    'update' => 'Ubah Payroll Component',
                    'delete' => 'Hapus Payroll Component',
                ],
            ],

            'payroll-slip'         => [
                'label'   => 'Payroll Slip',
                'actions' => [
                    'index'  => 'Lihat Payroll Slip',
                    'create' => 'Buat Payroll Slip',
                    'update' => 'Ubah Payroll Slip',
                    'delete' => 'Hapus Payroll Slip',
                ],
            ],

            'piutang-karyawan'     => [
                'label'   => 'Piutang Karyawan',
                'actions' => [
                    'index'  => 'Lihat Piutang Karyawan',
                    'create' => 'Buat Piutang Karyawan',
                    'update' => 'Ubah Piutang Karyawan',
                    'delete' => 'Hapus Piutang Karyawan',
                ],
            ],

            'master-training'      => [
                'label'   => 'Master Training',
                'actions' => [
                    'index'  => 'Lihat Master Training',
                    'create' => 'Buat Master Training',
                    'update' => 'Ubah Master Training',
                    'delete' => 'Hapus Master Training',
                ],
            ],
            'meeting'              => [
                'label'   => 'Meeting',
                'actions' => [
                    'index'  => 'Lihat Meeting',
                    'create' => 'Catat Meeting',
                    'update' => 'Ubah Meeting',
                    'delete' => 'Hapus Meeting',
                ],
            ],
        ],
        'OPERASIONAL' => [
            'pembelian'              => [
                'label'   => 'Pembelian',
                'actions' => [
                    'index'  => 'Lihat Pembelian',
                    'create' => 'Buat Pembelian',
                    'update' => 'Ubah Pembelian',
                    'delete' => 'Hapus Pembelian',
                ],
            ],
            'service'                => [
                'label'   => 'Service',
                'actions' => [
                    'index'  => 'Lihat Service',
                    'create' => 'Buat Service',
                    'update' => 'Ubah Service',
                    'delete' => 'Hapus Service',
                ],
            ],
            'pemakaian'              => [
                'label'   => 'Pemakaian',
                'actions' => [
                    'index'  => 'Lihat Pemakaian',
                    'create' => 'Buat Pemakaian',
                    'update' => 'Ubah Pemakaian',
                    'delete' => 'Hapus Pemakaian',
                ],
            ],
            'maintenance'            => [
                'label'   => 'Maintenance',
                'actions' => [
                    'index'  => 'Lihat Maintenance',
                    'create' => 'Buat Maintenance',
                    'update' => 'Ubah Maintenance',
                    'delete' => 'Hapus Maintenance',
                ],
            ],
            'warehouse'              => [
                'label'   => 'Warehouse',
                'actions' => [
                    'index'  => 'Lihat Warehouse',
                    'create' => 'Buat Warehouse',
                    'update' => 'Ubah Warehouse',
                    'delete' => 'Hapus Warehouse',
                ],
            ],
            'customer-prospek'       => [
                'label'   => 'Customer Prospek',
                'actions' => [
                    'index'  => 'Lihat Customer Prospek',
                    'create' => 'Buat Customer Prospek',
                    'update' => 'Ubah Customer Prospek',
                    'delete' => 'Hapus Customer Prospek',
                ],
            ],
            'piutang-customer'       => [
                'label'   => 'Piutang Customer',
                'actions' => [
                    'index' => 'Lihat Piutang Customer',
                ],
            ],
            'payment'                => [
                'label'   => 'Payment',
                'actions' => [
                    'index'  => 'Lihat Payment',
                    'create' => 'Buat Payment',
                    'update' => 'Ubah Payment',
                    'delete' => 'Hapus Payment',
                ],
            ],
            'vendor-prospek'         => [
                'label'   => 'Vendor Prospek',
                'actions' => [
                    'index'  => 'Lihat Vendor Prospek',
                    'create' => 'Buat Vendor Prospek',
                    'update' => 'Ubah Vendor Prospek',
                    'delete' => 'Hapus Vendor Prospek',
                ],
            ],
            'saldo-awal-barang'      => [
                'label'   => 'Saldo Awal Barang',
                'actions' => [
                    'index'  => 'Lihat Saldo Awal Barang',
                    'create' => 'Buat Saldo Awal Barang',
                    'update' => 'Ubah Saldo Awal Barang',
                    'delete' => 'Hapus Saldo Awal Barang',
                ],
            ],
            'barang-bekas'           => [
                'label'   => 'Barang Bekas',
                'actions' => [
                    'index'  => 'Lihat Barang Bekas',
                    'create' => 'Buat Barang Bekas',
                    'update' => 'Ubah Barang Bekas',
                    'delete' => 'Hapus Barang Bekas',
                ],
            ],
            'stock-opname-barang-bekas'           => [
                'label'   => 'Stock Opname Barang Bekas',
                'actions' => [
                    'index'  => 'Lihat Stock Opname Barang Bekas',
                    'create' => 'Buat Stock Opname Barang Bekas',
                    'update' => 'Ubah Stock Opname Barang Bekas',
                    'delete' => 'Hapus Stock Opname Barang Bekas',
                ],
            ],
            'spk'                    => [
                'label'   => 'SPK',
                'actions' => [
                    'index'  => 'Lihat Spk',
                    'create' => 'Buat Spk',
                    'update' => 'Ubah Spk',
                    'delete' => 'Hapus Spk',
                ],
            ],
            'tindak-lanjut'          => [
                'label'   => 'Tindak Lanjut',
                'actions' => [
                    'index'  => 'Lihat Tindak Lanjut',
                    'create' => 'Buat Tindak Lanjut',
                    'update' => 'Ubah Tindak Lanjut',
                    'delete' => 'Hapus Tindak Lanjut',
                ],
            ],
            'pembelian-aset'         => [
                'label'   => 'Pembelian Aset',
                'actions' => [
                    'index'  => 'Lihat Pembelian Aset',
                    'create' => 'Buat Pembelian Aset',
                    'update' => 'Ubah Pembelian Aset',
                    'delete' => 'Hapus Pembelian Aset',
                ],
            ],
            'laporan-pembelian'      => [
                'label'   => 'Laporan Pembelian',
                'actions' => [
                    'index'  => 'Lihat Laporan Pembelian',
                    'create' => 'Buat Laporan Pembelian',
                    'update' => 'Ubah Laporan Pembelian',
                    'delete' => 'Hapus Laporan Pembelian',
                ],
            ],
            'laporan-pemakaian'      => [
                'label'   => 'Laporan Pemakaian',
                'actions' => [
                    'index'  => 'Lihat Laporan Pemakaian',
                    'create' => 'Buat Laporan Pemakaian',
                    'update' => 'Ubah Laporan Pemakaian',
                    'delete' => 'Hapus Laporan Pemakaian',
                ],
            ],
            'lap-harian'             => [
                'label'   => 'Laporan Harian',
                'actions' => [
                    'index' => 'Lihat Laporan Harian',
                ],
            ],
        ],
        'KEUANGAN'    => [
            'coa'      => [
                'label'   => 'Chart of Account (CoA)',
                'actions' => [
                    'index'  => 'Lihat CoA',
                    'update' => 'Ubah CoA',
                    'delete' => 'Hapus CoA',
                ],
            ],
            'neraca'           => [
                'label'   => 'Neraca',
                'actions' => [
                    'index'  => 'Lihat Neraca',
                    'create' => 'Buat Neraca',
                    'update' => 'Ubah Neraca',
                    'delete' => 'Hapus Neraca',
                ],
            ],
            'rugilaba'         => [
                'label'   => 'Rugi Laba',
                'actions' => [
                    'index'  => 'Lihat Rugi Laba',
                    'create' => 'Buat Rugi Laba',
                    'update' => 'Ubah Rugi Laba',
                    'delete' => 'Hapus Rugi Laba',
                ],
            ],
            'jurnal'           => [
                'label'   => 'Jurnal',
                'actions' => [
                    'index'  => 'Lihat Jurnal',
                    'create' => 'Buat Jurnal',
                    'update' => 'Ubah Jurnal',
                    'delete' => 'Hapus Jurnal',
                ],
            ],
            'bukubesar'        => [
                'label'   => 'Buku Besar',
                'actions' => [
                    'index'  => 'Lihat Buku Besar',
                    'create' => 'Buat Buku Besar',
                    'update' => 'Ubah Buku Besar',
                    'delete' => 'Hapus Buku Besar',
                ],
            ],
            'cashflow'         => [
                'label'   => 'Cashflow',
                'actions' => [
                    'index'  => 'Lihat Cashflow',
                    'create' => 'Buat Cashflow',
                    'update' => 'Ubah Cashflow',
                    'delete' => 'Hapus Cashflow',
                ],
            ],
            'invoice'          => [
                'label'   => 'Invoice',
                'actions' => [
                    'index'  => 'Lihat Invoice',
                    'create' => 'Buat Invoice',
                    'update' => 'Ubah Invoice',
                    'delete' => 'Hapus Invoice',
                ],
            ],
            'hutang-sparepart' => [
                'label'   => 'Hutang Sparepart',
                'actions' => [
                    'index'  => 'Lihat Hutang Sparepart',
                    'create' => 'Buat Hutang Sparepart',
                    'update' => 'Ubah Hutang Sparepart',
                    'delete' => 'Hapus Hutang Sparepart',
                ],
            ],
            'hutang-vendor'    => [
                'label'   => 'Hutang Vendor',
                'actions' => [
                    'index'  => 'Lihat Hutang Vendor',
                    'create' => 'Buat Hutang Vendor',
                    'update' => 'Ubah Hutang Vendor',
                    'delete' => 'Hapus Hutang Vendor',
                ],
            ],
            'memorial'         => [
                'label'   => 'Memorial',
                'actions' => [
                    'index'  => 'Lihat Memorial',
                    'create' => 'Buat Memorial',
                    'update' => 'Ubah Memorial',
                    'delete' => 'Hapus Memorial',
                ],
            ],
            'tutupbuku'        => [
                'label'   => 'Tutup Buku',
                'actions' => [
                    'index'  => 'Lihat Tutup Buku',
                    'create' => 'Buat Tutup Buku',
                    'update' => 'Ubah Tutup Buku',
                    'delete' => 'Hapus Tutup Buku',
                ],
            ],
            'kas-bank-keluar'  => [
                'label'   => 'Kas Bank Keluar',
                'actions' => [
                    'index'  => 'Lihat Kas Bank Keluar',
                    'create' => 'Buat Kas Bank Keluar',
                    'update' => 'Ubah Kas Bank Keluar',
                    'delete' => 'Hapus Kas Bank Keluar',
                ],
            ],
            'kas-bank-masuk'   => [
                'label'   => 'Kas Bank Masuk',
                'actions' => [
                    'index'  => 'Lihat Kas Bank Masuk',
                    'create' => 'Buat Kas Bank Masuk',
                    'update' => 'Ubah Kas Bank Masuk',
                    'delete' => 'Hapus Kas Bank Masuk',
                ],
            ],
            'rekonsiliasi-bank'   => [
                'label'   => 'Rekonsiliasi Bank',
                'actions' => [
                    'index'  => 'Lihat Rekonsiliasi Bank',
                    'create' => 'Buat Rekonsiliasi Bank',
                    'update' => 'Ubah Rekonsiliasi Bank',
                    'delete' => 'Hapus Rekonsiliasi Bank',
                ],
            ],
            'laporan-kas-bank' => [
                'label'   => 'Laporan Kas Bank',
                'actions' => [
                    'index'  => 'Lihat Laporan Kas Bank',
                    'create' => 'Buat Laporan Kas Bank',
                    'update' => 'Ubah Laporan Kas Bank',
                    'delete' => 'Hapus Laporan Kas Bank',
                ],
            ],
            'laporan-omset-customer' => [
                'label'   => 'Laporan Omset Customer',
                'actions' => [
                    'index'  => 'Lihat Laporan Omset Customer',
                    'create' => 'Buat Laporan Omset Customer',
                    'update' => 'Ubah Laporan Omset Customer',
                    'delete' => 'Hapus Laporan Omset Customer',
                ],
            ],
            'kartu-piutang'    => [
                'label'   => 'Kartu Piutang',
                'actions' => [
                    'index' => 'Lihat Kartu Piutang',
                ],
            ],
            'laporan-hutang'   => [
                'label'   => 'Laporan Hutang',
                'actions' => [
                    'index' => 'Lihat Laporan Hutang',
                ],
            ],
        ],
        'MASTER'      => [
            'tokos'       => [
                'label'   => 'Toko',
                'actions' => [
                    'index'  => 'Lihat Toko',
                    'create' => 'Buat Toko',
                    'update' => 'Ubah Toko',
                    'delete' => 'Hapus Toko',
                ],
            ],
            'produks'     => [
                'label'   => 'Produk',
                'actions' => [
                    'index'  => 'Lihat Produk',
                    'create' => 'Buat Produk',
                    'update' => 'Ubah Produk',
                    'delete' => 'Hapus Produk',
                ],
            ],
            'companies'   => [
                'label'   => 'Perusahaan',
                'actions' => [
                    'index'  => 'Lihat Perusahaan',
                    'create' => 'Buat Perusahaan',
                    'update' => 'Ubah Perusahaan',
                    'delete' => 'Hapus Perusahaan',
                ],
            ],
            'departemens' => [
                'label'   => 'Departemen',
                'actions' => [
                    'index'  => 'Lihat Departemen',
                    'create' => 'Buat Departemen',
                    'update' => 'Ubah Departemen',
                    'delete' => 'Hapus Departemen',
                ],
            ],
            'positions'   => [
                'label'   => 'Jabatan',
                'actions' => [
                    'index'  => 'Lihat Jabatan',
                    'create' => 'Buat Jabatan',
                    'update' => 'Ubah Jabatan',
                    'delete' => 'Hapus Jabatan',
                ],
            ],
            'customer' => [
                'label'   => 'Customer',
                'actions' => [
                    'index'  => 'Lihat Customer',
                    'create' => 'Buat Customer',
                    'update' => 'Ubah Customer',
                    'delete' => 'Hapus Customer',
                ],
            ],
            'vendor'   => [
                'label'   => 'Vendor',
                'actions' => [
                    'index'  => 'Lihat Vendor',
                    'create' => 'Buat Vendor',
                    'update' => 'Ubah Vendor',
                    'delete' => 'Hapus Vendor',
                ],
            ],
            'barang'   => [
                'label'   => 'Barang',
                'actions' => [
                    'index'  => 'Lihat Barang',
                    'create' => 'Buat Barang',
                    'update' => 'Ubah Barang',
                    'delete' => 'Hapus Barang',
                ],
            ],
            'wilayah-provinsi' => [
                'label'   => 'Provinsi',
                'actions' => [
                    'index'  => 'Lihat Provinsi',
                    'create' => 'Buat Provinsi',
                    'update' => 'Ubah Provinsi',
                    'delete' => 'Hapus Provinsi',
                ],
            ],
            'wilayah-kota-kab' => [
                'label'   => 'Kota / Kabupaten',
                'actions' => [
                    'index'  => 'Lihat Kota/Kabupaten',
                    'create' => 'Buat Kota/Kabupaten',
                    'update' => 'Ubah Kota/Kabupaten',
                    'delete' => 'Hapus Kota/Kabupaten',
                ],
            ],
            'wilayah-kecamatan' => [
                'label'   => 'Kecamatan',
                'actions' => [
                    'index'  => 'Lihat Kecamatan',
                    'create' => 'Buat Kecamatan',
                    'update' => 'Ubah Kecamatan',
                    'delete' => 'Hapus Kecamatan',
                ],
            ],
            'wilayah-desa' => [
                'label'   => 'Desa / Kelurahan',
                'actions' => [
                    'index'  => 'Lihat Desa',
                    'create' => 'Buat Desa',
                    'update' => 'Ubah Desa',
                    'delete' => 'Hapus Desa',
                ],
            ],
            'layanan' => [
                'label'   => 'Layanan',
                'actions' => [
                    'index'  => 'Lihat Layanan',
                    'create' => 'Buat Layanan',
                    'update' => 'Ubah Layanan',
                    'delete' => 'Hapus Layanan',
                ],
            ],
        ],
        'TUGAS'       => [
            'my-tasks'    => [
                'label'   => 'Tugas Saya',
                'actions' => [
                    'index'  => 'Lihat Tugas Saya',
                ],
            ],
            'tasks'       => [
                'label'   => 'Daftar Tugas',
                'actions' => [
                    'index'  => 'Lihat Tugas',
                    'create' => 'Buat Tugas',
                    'update' => 'Ubah Tugas',
                    'delete' => 'Hapus Tugas',
                ],
            ],
            'input-omset' => [
                'label'   => 'Input Omset',
                'actions' => [
                    'index'  => 'Lihat Input Omset',
                    'create' => 'Buat Input Omset',
                    'update' => 'Ubah Input Omset',
                    'delete' => 'Hapus Input Omset',
                ],
            ],
            'setting-fee-omset' => [
                'label'   => 'Setting Fee Omset',
                'actions' => [
                    'index'  => 'Lihat Setting Fee Omset',
                    'create' => 'Buat Setting Fee Omset',
                    'update' => 'Ubah Setting Fee Omset',
                    'delete' => 'Hapus Setting Fee Omset',
                ],
            ],
            'task-categories' => [
                'label'   => 'Kategori Tugas',
                'actions' => [
                    'index'  => 'Lihat Kategori Tugas',
                    'create' => 'Buat Kategori Tugas',
                    'update' => 'Ubah Kategori Tugas',
                    'delete' => 'Hapus Kategori Tugas',
                ],
            ],
            'task-priorities' => [
                'label'   => 'Prioritas Tugas',
                'actions' => [
                    'index'  => 'Lihat Prioritas Tugas',
                    'create' => 'Buat Prioritas Tugas',
                    'update' => 'Ubah Prioritas Tugas',
                    'delete' => 'Hapus Prioritas Tugas',
                ],
            ],
        ],
    ],

];

