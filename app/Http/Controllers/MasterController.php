<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
use App\Models\Perusahaan;
use App\Models\RoleUser;
use Modules\Operasional\Entities\Sopir;
use App\Models\Pelanggan;

class MasterController extends Controller
{
    protected $ApiLogin; 
    public function __construct(LoginController $LoginController)
    {
        $this->ApiLogin = $LoginController;
    }

    public function getPerush(Request $request)
    {
        $cek = $this->ApiLogin->CheckToken($request->token);

        if($cek == false){

            $result = [
                "message" => "Token Expired",
                "status"    => 1,
                "data"  => false
            ];

            return response()->json($result);
        }

        $data = Perusahaan::getPerush($request->id_perush);

		$result = [];
		if (!$data) {

			$result = [
                "message" => "Cabang Perusahaan Tidak Ditemukan",
                "status"    => 1,
                "data"  => []
            ];
            
		}else{

			$result = [
                "message" => "Data Sukses",
                "status"    => 0,
                "data"  => $data
            ];
            
		}
		
		return response()->json($result);
    }

    public function getRole(Request $request)
    {
        $cek = $this->ApiLogin->CheckToken($request->token);

        if($cek == false){

            $result = [
                "message" => "Token Expired",
                "status"    => 1,
                "data"  => false
            ];

            return response()->json($result);
        }

        $data = RoleUser::ChekRole($request->id_user);

        $result = [];
		if (!$data) {

			$result = [
                "message" => "Role Tidak Ditemukan",
                "status"    => 1,
                "data"  => []
            ];
            
		}else{

			$result = [
                "message" => "Data Sukses",
                "status"    => 0,
                "data"  => $data
            ];
            
		}
		
		return response()->json($result);
    }
    
    public function getDriver(Request $request)
    {
        $cek = $this->ApiLogin->CheckToken($request->token);

        if($cek == false){

            $result = [
                "message" => "Token Expired",
                "status"    => 1,
                "data"  => false
            ];

            return response()->json($result);
        }

        $data = Sopir::getDriver($request->id_user);

        $result = [];
		if (!$data) {

			$result = [
                "message" => "Driver Tidak Ditemukan",
                "status"    => 1,
                "data"  => []
            ];
            
		}else{

			$result = [
                "message" => "Data Sukses",
                "status"    => 0,
                "data"  => $data
            ];
            
		}
		
		return response()->json($result);
    }

    public function getPelanggan(Request $request)
    {
        $cek = $this->ApiLogin->CheckToken($request->token);
        
        if($cek == false){

            $result = [
                "message" => "Token Expired",
                "status"    => 1,
                "data"  => false
            ];

            return response()->json($result);
        }

        $data = Pelanggan::getPelanggan($request->id_perush);
        
		$result = [];
		if (!$data) {

			$result = [
                "message" => "Cabang Perusahaan Tidak Ditemukan",
                "status"    => 1,
                "data"  => []
            ];
            
		}else{

			$result = [
                "message" => "Data Sukses",
                "status"    => 0,
                "data"  => $data
            ];
            
		}
		
		return response()->json($result);
    }
}
