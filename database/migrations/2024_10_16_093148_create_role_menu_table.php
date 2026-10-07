<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
        });

        $exclude = ['data', 'ajax', 'debug', 'livewire', 'ignition'];
        $routes  = collect(Route::getRoutes())
            ->filter(fn($route) => ! Str::contains($route->uri(), $exclude))
            ->map(fn($route) => $route->getName())
            ->filter()
            ->values()
            ->all();

        foreach ($routes as $r) {
            Permission::firstOrCreate(['name' => $r], ['label' => ucfirst(str_replace('.', ' ', $r))]);
        }

        Schema::create('role_menu', function (Blueprint $table) {
            $table->integer('id_rm', true);
            $table->integer('id_role')->index('role_menu_id_role_idx');
            $table->string('permission')->index('role_menu_permission_idx');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
        });

        $roles              = DB::table('role')->get();
        $allPermissions     = config('constant.permissions');
        $operasionalModules = [
            'armada',
            'do',
            'pembelian',
            'service',
            'pemakaian',
            'maintenance',
            'warehouse',
            'customer',
            'vendor',
        ];

        $karyawanModules = [
            'absensi',
            'employee-activities',
            'izin'
        ];

        $data = [];
        foreach ($roles as $role) {
            foreach ($allPermissions as $module => $submodules) {
                foreach ($submodules as $prefix => $routes) {
                    // Filter for 'Operasional' role if necessary
                    if ($role->nm_role === 'Operasional' && ! in_array($prefix, $operasionalModules)) {
                        continue;
                    }

                    if ($role->nm_role === 'Karyawan' && ! in_array($prefix, $karyawanModules)) {
                        continue;
                    }

                    foreach ($routes['actions'] as $key => $route) {
                        $method = $prefix . '.' . $key;
                        $data[] = [
                            'id_role'    => $role->id_role,
                            'permission' => $method,
                        ];
                    }
                }
            }
        }
        DB::table('role_menu')->insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_menu');
        Schema::dropIfExists('permissions');
    }
};
