<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Departemen;
use Modules\Master\Entities\Karyawan;
use Modules\Master\Entities\Position;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['code' => 'PAP'],
            [
                'name'      => 'PT Putera Adhi Perkasa',
                'email'     => 'admin@paphardware.com',
                'timezone'  => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );

        $deptOps = Departemen::where('code', 'OPS')->value('id');
        $deptMkt = Departemen::where('code', 'IT-MKT')->value('id');
        $deptLog = Departemen::where('code', 'LOG')->value('id');
        $deptFin = Departemen::where('code', 'FIN')->value('id');

        $posSpv = Position::where('code', 'SPV-MKT')->value('id');
        $posOps = Position::where('code', 'STF-OPS')->value('id');
        $posMkt = Position::where('code', 'STF-MKT')->value('id');
        $posLog = Position::where('code', 'STF-LOG')->value('id');
        $posFin = Position::where('code', 'STF-FIN')->value('id');

        $employees = [
            [
                'employee_number'   => 'EMP-001',
                'name'              => 'Meryana',
                'nama_lengkap'      => 'Meryana',
                'nik_ktp'           => '3578266103770001',
                'email'             => 'meryana@paphardware.com',
                'phone'             => '081234567001',
                'nomor_hp'          => '081234567001',
                'gender'            => 'Perempuan',
                'jenis_kelamin'     => 'Perempuan',
                'birth_date'        => '1992-03-21',
                'tanggal_lahir'     => '1992-03-21',
                'address'           => 'Jl. Mawar No. 12, Surabaya',
                'alamat_ktp'        => 'Jl. Mawar No. 12, Surabaya',
                'department_id'     => $deptOps,
                'position_id'       => $posSpv,
                'jabatan'           => 'Staff Operasional',
                'status'            => 'aktif',
                'is_active'         => true,
                'employment_status' => 'Tetap',
                'join_date'         => '2022-01-15',
            ],
            [
                'employee_number'   => 'EMP-002',
                'name'              => 'Budi Santoso',
                'nama_lengkap'      => 'Budi Santoso',
                'nik_ktp'           => '3517121502890002',
                'email'             => 'budi.santoso@paphardware.com',
                'phone'             => '081234567002',
                'nomor_hp'          => '081234567002',
                'gender'            => 'Laki-laki',
                'jenis_kelamin'     => 'Laki-laki',
                'birth_date'        => '1990-02-15',
                'tanggal_lahir'     => '1990-02-15',
                'address'           => 'Jl. Kenanga No. 8, Surabaya',
                'alamat_ktp'        => 'Jl. Kenanga No. 8, Surabaya',
                'department_id'     => $deptOps,
                'position_id'       => $posOps,
                'jabatan'           => 'Staff Operasional',
                'status'            => 'aktif',
                'is_active'         => true,
                'employment_status' => 'Tetap',
                'join_date'         => '2022-03-01',
            ],
            [
                'employee_number'   => 'EMP-003',
                'name'              => 'Citra Ayu Lestari',
                'nama_lengkap'      => 'Citra Ayu Lestari',
                'nik_ktp'           => '3517123003990003',
                'email'             => 'citra.ayu@paphardware.com',
                'phone'             => '081234567003',
                'nomor_hp'          => '081234567003',
                'gender'            => 'Perempuan',
                'jenis_kelamin'     => 'Perempuan',
                'birth_date'        => '1995-07-20',
                'tanggal_lahir'     => '1995-07-20',
                'address'           => 'Jl. Cemara No. 21, Sidoarjo',
                'alamat_ktp'        => 'Jl. Cemara No. 21, Sidoarjo',
                'department_id'     => $deptMkt,
                'position_id'       => $posMkt,
                'jabatan'           => 'Marketing',
                'status'            => 'aktif',
                'is_active'         => true,
                'employment_status' => 'Tetap',
                'join_date'         => '2022-06-10',
            ],
            [
                'employee_number'   => 'EMP-004',
                'name'              => 'Dedi Prakoso',
                'nama_lengkap'      => 'Dedi Prakoso',
                'nik_ktp'           => '3517120404950004',
                'email'             => 'dedi.prakoso@paphardware.com',
                'phone'             => '081234567004',
                'nomor_hp'          => '081234567004',
                'gender'            => 'Laki-laki',
                'jenis_kelamin'     => 'Laki-laki',
                'birth_date'        => '1993-11-04',
                'tanggal_lahir'     => '1993-11-04',
                'address'           => 'Jl. Melati No. 11, Gresik',
                'alamat_ktp'        => 'Jl. Melati No. 11, Gresik',
                'department_id'     => $deptLog,
                'position_id'       => $posLog,
                'jabatan'           => 'Staff Operasional',
                'status'            => 'aktif',
                'is_active'         => true,
                'employment_status' => 'Tetap',
                'join_date'         => '2023-01-05',
            ],
            [
                'employee_number'   => 'EMP-005',
                'name'              => 'Eka Sari Putri',
                'nama_lengkap'      => 'Eka Sari Putri',
                'nik_ktp'           => '3517121212970005',
                'email'             => 'eka.sari@paphardware.com',
                'phone'             => '081234567005',
                'nomor_hp'          => '081234567005',
                'gender'            => 'Perempuan',
                'jenis_kelamin'     => 'Perempuan',
                'birth_date'        => '1997-12-12',
                'tanggal_lahir'     => '1997-12-12',
                'address'           => 'Jl. Rajawali No. 5, Pasuruan',
                'alamat_ktp'        => 'Jl. Rajawali No. 5, Pasuruan',
                'department_id'     => $deptFin,
                'position_id'       => $posFin,
                'jabatan'           => 'Staff Keuangan',
                'status'            => 'aktif',
                'is_active'         => true,
                'employment_status' => 'Tetap',
                'join_date'         => '2023-02-15',
            ],
        ];

        foreach ($employees as $empData) {
            $empData['company_id'] = $company->id;
            
            // Check by employee_number or nik_ktp to avoid duplicate constraint violations
            $existing = Karyawan::where('employee_number', $empData['employee_number'])
                ->orWhere('nik_ktp', $empData['nik_ktp'])
                ->first();

            if ($existing) {
                $existing->update($empData);
            } else {
                Karyawan::create($empData);
            }
        }
    }
}
