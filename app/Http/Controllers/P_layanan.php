<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
Use Response;
use App\Models\Layanan;
use App\Http\Requests\RequestLayanan;
use DB;
use Auth;

class P_layanan extends Controller
{
    public function index()
    {
        $data["data"] = Layanan::paginate(10);

        return view("layanan", $data);
    }

    public function create()
    {  
        return view("layanan");
    }
    
    public function store(RequestLayanan $request)
    {
        try {
            
            // save to layanan
            DB::beginTransaction();
            $layanan                       = new Layanan();
            $layanan->nm_layanan = $request->nm_layanan;
            $layanan->kode_layanan = strtoupper($request->kode_layanan);
            $layanan->id_user = Auth::user()->id_user;
            $layanan->save();
            
            DB::commit();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Data layanan gagal Disimpan');
        }

        return redirect("layanan")->with('success', 'Data layanan Disimpan');
    }

    public function show($id)
    {
        abort(404);
    }

    public function edit($id)
    {
        $data["data"] = Layanan::find($id);

        return view("layanan", $data);
    }

    public function update(RequestLayanan $request, $id)
    {   
        try {
            
            // save to layanan
            DB::beginTransaction();

            $layanan                       = Layanan::findOrFail($id);
            $layanan->kode_layanan           = $request->kode_layanan;
            $layanan->nm_layanan           = $request->nm_layanan;
            $layanan->kode_layanan         = strtoupper($request->kode_layanan);
            $layanan->id_user              = Auth::user()->id_user;
            $layanan->save();
            
            DB::commit();

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Data layanan gagal Disimpan'.$e->getMessage());
        }

        return redirect("layanan")->with('success', 'Data layanan Disimpan');
    }

    public function destroy($id)
    {   
        try{
            
            $layanan = Layanan::findOrFail($id);
            $layanan->delete();
            
        } catch (Exception $e) {

           return redirect()->back()->with('error', 'Data masih digunakan di table lain');
       }
       
       return redirect()->back()->with('success', 'Data layanan Disimpan');
   }

}
