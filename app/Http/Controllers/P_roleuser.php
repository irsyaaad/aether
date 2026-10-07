<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
Use Response;
use App\Models\RoleUser;
use DB;
use Validator;
use App\Http\Requests\RequestRoleUser;
use App\Models\Role;
use App\Models\User;
use Auth;
use Session;
use App\Models\Perusahaan;
use App\Models\Karyawan;

class P_roleuser extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page?$request->page:1;
        $perpage = $request->shareselect?$request->shareselect:50;
        $id_perush = $request->f_id_perush?$request->f_id_perush:null;
        $id_role = $request->f_id_role;
        $id_user = $request->f_id_user;

        $data["data"] = RoleUser::getFilter($page, $perpage, $id_role, $id_perush, $id_user);
        $data["role"] = Role::select("id_role", "nm_role")->orderBy("nm_role", "asc")->get();
        $data["user"] = User::with("karyawan")->get();
        $data["perusahaan"] = Perusahaan::select("id_perush", "nm_perush")->orderBy("nm_perush", "asc")->get();
        $data["filter"] = array("page" => $perpage, "f_id_perush" => $id_perush, "f_id_role"=>$id_role, 'f_id_user' => $id_user);
        
        return view("roleuser", $data);
    }

    public function filter(Request $request)
    {
        $page = $request->page?$request->page:1;
        $perpage = $request->shareselect?$request->shareselect:50;
        $id_perush = $request->f_id_perush?$request->f_id_perush:null;
        $id_role = $request->f_id_role;
        $id_user = $request->f_id_user;

        $data["data"] = RoleUser::getFilter($page, $perpage, $id_role, $id_perush, $id_user);
        $data["role"] = Role::select("id_role", "nm_role")->orderBy("nm_role", "asc")->get();
        $data["user"] = User::with("karyawan")->get();
        $data["perusahaan"] = Perusahaan::select("id_perush", "nm_perush")->orderBy("nm_perush", "asc")->get();
        $data["filter"] = array("page" => $perpage, "f_id_perush" => $id_perush, "f_id_role"=>$id_role, 'f_id_user' => $id_user);
        
        return view("roleuser", $data);
    }
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        $data["role"] = Role::all();
        $data["user"] = Karyawan::getkaryawanuser();
        $data["data"] = [];
        $data["perusahaan"] = Perusahaan::select("id_perush", "nm_perush")->get();
        
        return view("create-role", $data);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(RequestRoleUser $request)
    {
        // check if exsist
        $chek = RoleUser::where("id_role", $request->id_role)->where("id_user", $request->id_user)->where("id_perush", $request->id_perush)->first();
        
        if($chek!=null){
            return redirect()->back()->with('error', 'Role Sudah Ada')->withInput($request->all());
        }

        try {
            // save to user
            DB::beginTransaction();

            $role               = new RoleUser();
            $role->id_user      = $request->id_user;
            $role->id_role      = $request->id_role;
            $role->id_perush    = $request->id_perush;

            $role->save();

            DB::commit();
        } catch (Exception $e) {

            return redirect()->back()->with('error', 'Data Role User Gagal Disimpan')->withInput($request->all());
        }

        return redirect("roleuser")->with('success', 'Data Role User Disimpan');
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        abort(404);
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id)
    {
        $data["role"] = Role::all();
        $data["user"] = Karyawan::getkaryawanuser();
        $data["data"] = RoleUser::findorFail($id);
        $data["perusahaan"] = Perusahaan::select("id_perush", "nm_perush")->get();

        return view("create-role", $data);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(RequestRoleUser $request, $id)
    {
        $chek = RoleUser::where("id_role", $request->id_role)->where("id_user", $request->id_user)->where("id_perush", $request->id_perush)->first();
        
        if($chek!=null){
            return redirect()->back()->with('error', 'Role Sudah Ada')->withInput($request->all());
        }
        
        try {
            // save to user
            DB::beginTransaction();

            RoleUser::where("id_ru", $id)->update(
                [
                    'id_user' => $request->id_user,
                    'id_role' => $request->id_role,
                    'id_perush' => $request->id_perush
                    ]);

                    DB::commit();

                } catch (Exception $e) {
                    return redirect()->back()->with('error', 'Data Role User Gagal Disimpan');
                }

                return redirect("roleuser")->with('success', 'Data Role User Disimpan');
            }

            /**
            * Remove the specified resource from storage.
            *
            * @param  int  $id
            * @return \Illuminate\Http\Response
            */
            public function destroy($id)
            {
                try{

                    $rol = RoleUser::findOrFail($id);
                    $rol->delete();

                } catch (Exception $e) {

                    return redirect()->back()->with('error', 'Data masih digunakan di table lain');
                }

                return redirect()->back()->with('success', 'Data Role User Di hapus');
            }

            public function changerole($id)
            {
                $role["role"] = Role::find($id)->toArray();
                //dd($role);
                Session($role);

                return redirect("dashboard")->with('success', 'Anda Login Sebagai '.$role["role"]["nm_role"]." ".Session("perusahaan")["nm_perush"]);
            }

            public function changeperush($id)
            {
                $role = [];
                $role["perusahaan"] = Perusahaan::select("id_perush", "nm_perush", "cabang")->where("id_perush", $id)->first()->toArray();

                Session($role);

                return redirect("dashboard")->with('success', 'Anda Memilih '.$role["perusahaan"]["nm_perush"]);
            }
        }
