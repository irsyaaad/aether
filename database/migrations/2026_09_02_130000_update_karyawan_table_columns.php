<?php

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
        Schema::table('karyawan', function (Blueprint $table) {
            if (!Schema::hasColumn('karyawan', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->index('company_id');
            }
            if (!Schema::hasColumn('karyawan', 'employee_number')) {
                $table->string('employee_number')->nullable()->unique()->after('company_id');
            }
            if (!Schema::hasColumn('karyawan', 'name')) {
                $table->string('name')->nullable()->after('employee_number');
            }
            if (!Schema::hasColumn('karyawan', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('karyawan', 'gender')) {
                $table->string('gender')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('karyawan', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('karyawan', 'address')) {
                $table->text('address')->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('karyawan', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('address');
                $table->index('department_id');
            }
            if (!Schema::hasColumn('karyawan', 'position_id')) {
                $table->unsignedBigInteger('position_id')->nullable()->after('department_id');
                $table->index('position_id');
            }
            if (!Schema::hasColumn('karyawan', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')->nullable()->after('position_id');
                $table->index('manager_id');
            }
            if (!Schema::hasColumn('karyawan', 'join_date')) {
                $table->date('join_date')->nullable()->after('manager_id');
            }
            if (!Schema::hasColumn('karyawan', 'employment_status')) {
                $table->string('employment_status')->default('Tetap')->nullable()->after('join_date');
            }
            if (!Schema::hasColumn('karyawan', 'avatar')) {
                $table->string('avatar')->nullable()->after('employment_status');
            }
            if (!Schema::hasColumn('karyawan', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('avatar');
                $table->index('is_active');
            }
        });

        // Sync legacy values to new columns for existing records
        DB::table('karyawan')->whereNull('name')->update([
            'name'              => DB::raw('nama_lengkap'),
            'phone'             => DB::raw('nomor_hp'),
            'gender'            => DB::raw('jenis_kelamin'),
            'birth_date'        => DB::raw('tanggal_lahir'),
            'address'           => DB::raw('alamat_ktp'),
            'avatar'            => DB::raw('foto_diri'),
            'is_active'         => DB::raw("CASE WHEN status = 'aktif' THEN 1 ELSE 0 END"),
            'employment_status' => 'Tetap',
        ]);

        // Generate employee_number for any existing rows that don't have one
        $rows = DB::table('karyawan')->whereNull('employee_number')->orWhere('employee_number', '')->get();
        foreach ($rows as $index => $row) {
            DB::table('karyawan')->where('id', $row->id)->update([
                'employee_number' => sprintf('EMP-%04d', $row->id),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $columns = [
                'company_id',
                'employee_number',
                'name',
                'phone',
                'gender',
                'birth_date',
                'address',
                'department_id',
                'position_id',
                'manager_id',
                'join_date',
                'employment_status',
                'avatar',
                'is_active',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('karyawan', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
