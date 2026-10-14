<?php

return [

    'permissions' => [
        'DASHBOARD' => [
            'dashboard' => [
                'label'   => 'Dashboard (Monitoring Riset Mahasiswa)',
                'actions' => [
                    'index' => 'Lihat Dashboard',
                ],
            ],
        ],

        'LAYANAN AKADEMIK' => [
            'bimbingan' => [
                'label'   => 'Bimbingan Skripsi',
                'actions' => [
                    'index'  => 'Lihat Bimbingan',
                    'create' => 'Ajukan Bimbingan',
                    'update' => 'Ubah Bimbingan',
                    'delete' => 'Hapus Bimbingan',
                ],
            ],
            'olahdata' => [
                'label'   => 'Olah Data Riset',
                'actions' => [
                    'index'  => 'Lihat Olah Data',
                    'create' => 'Upload Dataset Baru',
                    'update' => 'Ubah Olah Data',
                    'delete' => 'Hapus Olah Data',
                ],
            ],
            'pelatihan' => [
                'label'   => 'Pelatihan & Workshop',
                'actions' => [
                    'index'  => 'Lihat Pelatihan',
                    'create' => 'Buat Pelatihan',
                    'update' => 'Ubah Pelatihan',
                    'delete' => 'Hapus Pelatihan',
                ],
            ],
        ],

        'MANAJEMEN TUGAS' => [
            'my-tasks' => [
                'label'   => 'Tugas Saya',
                'actions' => [
                    'index' => 'Lihat Tugas Saya',
                ],
            ],
            'tasks' => [
                'label'   => 'Daftar Tugas',
                'actions' => [
                    'index'  => 'Lihat Tugas',
                    'create' => 'Buat Tugas',
                    'update' => 'Ubah Tugas',
                    'delete' => 'Hapus Tugas',
                ],
            ],
            'task-assignment' => [
                'label'   => 'Plotting Tugas',
                'actions' => [
                    'index'  => 'Lihat Plotting Tugas',
                    'create' => 'Buat Plotting Tugas',
                    'update' => 'Ubah Plotting Tugas',
                    'delete' => 'Hapus Plotting Tugas',
                ],
            ],
            'task-monitoring' => [
                'label'   => 'Monitoring Submission',
                'actions' => [
                    'index'  => 'Lihat Monitoring Submission',
                    'update' => 'Ubah Monitoring Submission',
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

        'MASTER DATA' => [
            'master.programs' => [
                'label'   => 'Master Program Riset',
                'actions' => [
                    'index'  => 'Lihat Master Program',
                    'create' => 'Buat Master Program',
                    'update' => 'Ubah Master Program',
                    'delete' => 'Hapus Master Program',
                ],
            ],
            'master.mentors' => [
                'label'   => 'Master Mentor Ahli',
                'actions' => [
                    'index'  => 'Lihat Master Mentor',
                    'create' => 'Buat Master Mentor',
                    'update' => 'Ubah Master Mentor',
                    'delete' => 'Hapus Master Mentor',
                ],
            ],
            'master.kelas' => [
                'label'   => 'Master Kelas & Workshop',
                'actions' => [
                    'index'  => 'Lihat Master Kelas',
                    'create' => 'Buat Master Kelas',
                    'update' => 'Ubah Master Kelas',
                    'delete' => 'Hapus Master Kelas',
                ],
            ],
            'master.materi' => [
                'label'   => 'Master Materi Silabus',
                'actions' => [
                    'index'  => 'Lihat Master Materi',
                    'create' => 'Buat Master Materi',
                    'update' => 'Ubah Master Materi',
                    'delete' => 'Hapus Master Materi',
                ],
            ],
            'master.metode' => [
                'label'   => 'Master Metode Riset',
                'actions' => [
                    'index'  => 'Lihat Master Metode',
                    'create' => 'Buat Master Metode',
                    'update' => 'Ubah Master Metode',
                    'delete' => 'Hapus Master Metode',
                ],
            ],
        ],

        'KEUANGAN' => [
            'kas-bank-masuk' => [
                'label'   => 'Kas Bank Masuk',
                'actions' => [
                    'index'  => 'Lihat Kas Bank Masuk',
                    'create' => 'Buat Kas Bank Masuk',
                    'update' => 'Ubah Kas Bank Masuk',
                    'delete' => 'Hapus Kas Bank Masuk',
                ],
            ],
            'kas-bank-keluar' => [
                'label'   => 'Kas Bank Keluar',
                'actions' => [
                    'index'  => 'Lihat Kas Bank Keluar',
                    'create' => 'Buat Kas Bank Keluar',
                    'update' => 'Ubah Kas Bank Keluar',
                    'delete' => 'Hapus Kas Bank Keluar',
                ],
            ],
            'jurnal' => [
                'label'   => 'Jurnal Umum',
                'actions' => [
                    'index'  => 'Lihat Jurnal',
                    'create' => 'Buat Jurnal',
                    'update' => 'Ubah Jurnal',
                    'delete' => 'Hapus Jurnal',
                    'cetak'  => 'Cetak Jurnal',
                    'export' => 'Export Excel Jurnal',
                ],
            ],
            'neraca' => [
                'label'   => 'Neraca Keuangan',
                'actions' => [
                    'index'  => 'Lihat Neraca',
                    'cetak'  => 'Cetak Neraca',
                    'export' => 'Export Excel Neraca',
                ],
            ],
            'rugi-laba' => [
                'label'   => 'Laba Rugi',
                'actions' => [
                    'index'  => 'Lihat Laba Rugi',
                    'cetak'  => 'Cetak Laba Rugi',
                    'export' => 'Export Excel Laba Rugi',
                ],
            ],
            'cashflow' => [
                'label'   => 'Arus Kas (Cash Flow)',
                'actions' => [
                    'index'  => 'Lihat Cash Flow',
                    'cetak'  => 'Cetak Cash Flow',
                    'export' => 'Export Excel Cash Flow',
                ],
            ],
        ],

        'SETTING' => [
            'master.coa' => [
                'label'   => 'Master Chart of Account (CoA)',
                'actions' => [
                    'index'  => 'Lihat CoA',
                    'create' => 'Buat CoA',
                    'update' => 'Ubah CoA',
                    'delete' => 'Hapus CoA',
                ],
            ],
            'perusahaan' => [
                'label'   => 'Setting Akun / Perusahaan',
                'actions' => [
                    'index'  => 'Lihat Setting Akun',
                    'update' => 'Ubah Setting Akun',
                ],
            ],
            'user' => [
                'label'   => 'Manajemen User',
                'actions' => [
                    'index'  => 'Lihat User',
                    'create' => 'Buat User',
                    'update' => 'Ubah User',
                    'delete' => 'Hapus User',
                ],
            ],
            'role' => [
                'label'   => 'Manajemen Role',
                'actions' => [
                    'index'  => 'Lihat Role',
                    'create' => 'Buat Role',
                    'update' => 'Ubah Role',
                    'delete' => 'Hapus Role',
                ],
            ],
            'role-menu' => [
                'label'   => 'Hak Akses & Permission',
                'actions' => [
                    'index'  => 'Lihat Hak Akses',
                    'update' => 'Ubah Hak Akses',
                ],
            ],
        ],
    ],

];
