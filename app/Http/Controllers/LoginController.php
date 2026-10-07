<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RoleUser;
use Auth;
use Hash;

class LoginController extends Controller
{
    public function getLogin(Request $request)
    {
        $req = [];
        $req["username"] = $request->username;
        $req["password"] = $request->password;
        
        $result = [];
        if(Auth::validate($req)){
            $user = User::where("username", $req["username"])->get()->first();
            $token = $this->getToken($user);
            
            $result = [
                "message" => "Data Sukses",
                "status"    => 0,
                "data"  => $token
            ];
            
        }else{
            
            $result = [
                "message" => "Username Tidak Ditemukan",
                "status"    => 1,
                "data"  => []
            ];
        }
        
        return response()->json($result);
    }
    
    public function getPerush(Request $request)
    {
        $cek = $this->CheckToken($request->token);

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
    
    public function getToken($user)
    {
        $time = time();
        $token = [];
        $token["token"] = Hash::make($user->username.$time);
        $token["token_expired"] = date("Y-m-d", strtotime("+1 week"));
        
        User::where("id_user", $user->id_user)->update($token);

        $token["token"] = $token["token"];
        $token["token_expired"] = $token["token_expired"];
        $token["user"]["id_user"] = $user->id_user;
        $token["user"]["nm_user"] = $user->nm_user;
        $token["user"]["username"] = $user->username;
        $token["user"]["email"] = $user->email;
        $token["user"]["id_perush"] = $user->id_perush;

        return $token;
    }
    
    public function CheckToken($token)
    {
        $cek = User::where("token", $token)->get()->first();

        $res = false;
        if($cek){
            $date = time();
            $token = strtotime($cek->token_expired);

            if($date > $token){
                $res = false;
            }else{
                $res = true;
            }

        }else{
            $res = false;
        }
        
        return $res;
    }
    
    public function CheckRequestToken(Request $request)
    {
        $cek = User::where("token", $request->token)->get()->first();
        
        $result = [];
        
        if($cek){
            $date = time();
            $token = strtotime($cek->token_expired);
            if($date > $token){
                
                $result = [
                    "message" => "Token Expired",
                    "status"    => 1,
                    "data"  => false
                ];
                
            }else{
                
                $result = [
                    "message" => "Token Sukses",
                    "status"    => 0,
                    "data"  => true
                ];
                
            }
            
        }else{
            
            $result = [
                "message" => "Token Tidak Ditemukan",
                "status"    => 1,
                "data"  => false
            ];
            
        }
        
        return response()->json($result);
    }
    
    public function RequestToken(Request $request)
    {
        $cek = User::where("token", $request->token)->get()->first();
        
        $result = [];
        if($cek){
            
            $token = $this->getToken($cek);
            
            $result = [
                "message" => "Data Sukses",
                "status"    => 0,
                "data"  => $token
            ];
            
        }else{
            $result = [
                "message" => "Token Tidak Ditemukan",
                "status"    => 1,
                "data"  => []
            ];
        }
        
        return response()->json($result);
    }
}
