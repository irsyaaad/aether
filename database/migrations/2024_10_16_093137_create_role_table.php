<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->integer('id_role')->primary();
            $table->string('nm_role', 60);
            $table->string('app_id', 50)->nullable();
            $table->integer('id_creator');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
        });

        DB::table('role')->delete();

        DB::table('role')->insert(array(
            0 => array(
                'id_role' => 1,
                'nm_role' => 'Administrator',
                'id_creator' => '1',
            ),
            1 => array(
                'id_role' => 2,
                'nm_role' => 'Direktur',
                'id_creator' => '1',
            ),
            2 => array(
                'id_role' => 3,
                'nm_role' => 'Keuangan',
                'id_creator' => '1',
            ),
            3 => array(
                'id_role' => 4,
                'nm_role' => 'Operasional',
                'id_creator' => '1',
            ),
            4 => array(
                'id_role' => 5,
                'nm_role' => 'Sopir',
                'id_creator' => '1',
            ),
            5 => array(
                'id_role' => 99,
                'nm_role' => 'Karyawan',
                'id_creator' => '1',
            ),
        ));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
