<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id_user', true);
            $table->string('nm_user', 100)->nullable();
            $table->string('username', 100)->nullable();
            $table->string('telp', 16)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->rememberToken();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('users')->delete();

        DB::table('users')->insert([
            [
                'nm_user'  => 'MOCH IRSYADUL ANAM',
                'username' => 'irsyad7798@gmail.com',
                'email'    => 'irsyad7798@gmail.com',
                'password' => Hash::make('bismillah1'),
                'telp'     => '08674514312331',
            ],
            [
                'nm_user'  => 'Super Admin',
                'username' => 'admin@gmail.com',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'telp'     => null,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
