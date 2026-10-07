<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use App\Models\Module;
use DB;
use Exception;
use Illuminate\Http\Request;

class P_menu extends BaseController
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $f_id_module = $request->f_id_module;

        $data1 = Menu::where("level", "1")->orderby('temp')->get();
        $data2 = Menu::where("level", "2")->orderby('temp')->get();
        $data3 = Menu::where("level", "3")->orderby('temp')->get();

        $a_data2 = [];
        foreach ($data2 as $key => $value) {
            $a_data2[$value->parent][$value->id_menu] = $value;
        }

        $a_data3 = [];
        foreach ($data3 as $key => $value) {
            $a_data3[$value->parent][$value->id_menu] = $value;
        }

        $data["data1"] = $data1;
        $data["data2"] = $a_data2;
        $data["data3"] = $a_data3;

        return view("menu.index", $data);
    }

    public function filter(Request $request)
    {
        $f_id_module = $request->f_id_module;

        $data1 = Menu::where("level", "1")->orderby('temp');
        $data2 = Menu::where("level", "2")->orderby('temp');
        $data3 = Menu::where("level", "3")->orderby('temp');
        if ($f_id_module != null) {
            $data1 = $data1->where("id_module", $f_id_module);
            $data2 = $data2->where("id_module", $f_id_module);
            $data3 = $data3->where("id_module", $f_id_module);
        }

        $data1 = $data1->get();
        $data2 = $data2->get();
        $data3 = $data3->get();

        $a_data2 = [];
        foreach ($data2 as $key => $value) {
            $a_data2[$value->parent][$value->id_menu] = $value;
        }

        $a_data3 = [];
        foreach ($data3 as $key => $value) {
            $a_data3[$value->parent][$value->id_menu] = $value;
        }

        $data["data1"] = $data1;
        $data["data2"] = $a_data2;
        $data["data3"] = $a_data3;

        $data["filter"] = array("f_id_module" => $f_id_module);
        $data["module"] = Module::select("id_module", "nm_module")->get();

        return view("menu", $data);
    }

    public function create()
    {
        $data["menu"] = Menu::select("*")->orderBy("parent", "asc")->orderBy("nm_menu", "asc")->get();
        //dd($data);
        $data["module"] = Module::all();

        return view("menu", $data);
    }

    public function store(MenuRequest $request)
    {
        try {

            // save to user
            DB::beginTransaction();
            $menu = new Menu();
            $menu->nm_menu = $request->nm_menu;
            $menu->icon = $request->icon;
            $menu->id_module = $request->id_module;
            $menu->parent = $request->parent;
            $menu->tampil = $request->tampil;
            $menu->route = $request->route;
            $menu->controller = $request->controller;

            if ($menu->route == "menus" or $menu->route == "module") {
                return redirect()->back()->with('error', 'Acces Denied');
            }

            // chek if level
            $level = Menu::find($menu->parent);
            $menu->level = 1;

            // if ada level
            if ($level != null) {
                $menu->level = $level->level + 1;
            }

            if ($menu->level > 3) {
                return redirect()->back()->with('error', 'Level Menu Maksimal 3');
            }

            $temp = Menu::where("level", $menu->level)->where("id_module", $menu->id_module)->orderBy("temp", "desc")->get()->first();
            $menu->temp = 1;
            if (isset($temp->temp)) {
                $menu->temp = $temp->temp + 1;
            }

            $menu->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Data menu Gagal Disimpan' . $e->getMessage());
        }

        return redirect("menus")->with('success', 'Data menu Disimpan');
    }

    public function show($id)
    {
        $menu = Menu::with("module")->orderBy("temp")->get();

        $data["data"] = $menu;
        $data["filter"] = [];

        return view("menu", $data);
    }

    public function edit($id)
    {
        $data["data"] = Menu::find($id);
        $data["module"] = Module::all();
        $data["menu"] = Menu::select("*")->orderBy("parent", "asc")->orderBy("nm_menu", "asc")->get();

        return view("menu", $data);
    }

    public function update(MenuRequest $request, $id)
    {
        try {
            // save to menu
            DB::beginTransaction();
            $menu = Menu::findOrFail($id);
            $menu->nm_menu = $request->nm_menu;
            $menu->icon = $request->icon;
            $menu->id_module = $request->id_module;
            $menu->parent = $request->parent;
            $menu->tampil = $request->tampil;
            $menu->route = $request->route;
            $menu->controller = $request->controller;

            if ($menu->route == "menus" or $menu->route == "module") {
                return redirect()->back()->with('error', 'Acces Denied');
            }

            // chek if level
            $level = Menu::find($menu->parent);
            $menu->level = 1;

            // if ada level
            if ($level != null) {
                $menu->level = $level->level + 1;
            }

            if ($menu->level > 3) {
                return redirect()->back()->with('error', 'Level Menu Maksimal 3');
            }

            $temp = Menu::where("level", $menu->level)->where("id_module", $menu->id_module)->orderBy("temp", "desc")->get()->first();
            $menu->temp = 1;
            if (isset($temp->temp)) {
                $menu->temp = $temp->temp + 1;
            }

            $menu->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Data menu gagal disimpan' . $e->getMessage());
        }

        return redirect(route_redirect())->with('success', 'Data Berhail menu disimpan');
    }

    public function destroy($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            $parent = Menu::where("parent", $id)->get()->first();

            if ($menu->route == "menu" or $menu->route == "module") {
                return redirect()->back()->with('error', 'Acces Denied');
            }

            $menu->delete();

        } catch (Exception $e) {

            return redirect(route_redirect())->with('error', 'Data masih digunakan di table lain' . $e->getMessage());
        }

        return redirect(route_redirect())->with('success', 'Data Berhail Dihapus');
    }

    // create menu
    public function arrange()
    {
        try {

            DB::beginTransaction();
            $data = Menu::orderBy("id_module")->orderBy("nm_menu")->get();

            foreach ($data as $key => $value) {
                $pos = [];
                $pos["temp"] = ($key + 1);
                Menu::where("id_menu", $value->id_menu)->update($pos);
            }

            DB::commit();

        } catch (Exception $e) {

            return redirect(route_redirect())->with('error', 'Menu gagal di pindah posisi', $e->getMessage());
        }

        return redirect(route_redirect())->with('success', 'Menu berpindah posisi');
    }

    // go up menu
    public function goup($id)
    {
        try {
            DB::beginTransaction();
            $data1 = Menu::findOrFail($id);
            $data1level = $data1->level;
            $pos1 = $data1->temp;
            $pos2 = $data1->temp - 1;

            $data2 = Menu::where('temp', $pos2)->where('level', $data1level)->get()->first();
            $idnya = $data2->id_menu;

            if (!is_null($data1)) {
                $result = DB::Table('menu')->where('id_menu', $id)->update(
                    array(
                        'temp' => $pos2,
                    )
                );
            }

            if (!is_null($data2)) {
                $result = DB::Table('menu')->where('id_menu', $idnya)->update(
                    array(
                        'temp' => $pos1,
                    )
                );
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return redirect(route_redirect())->with('error', 'Menu gagal di pindah posisi', $e->getMessage());
        }

        return redirect(route_redirect())->with('success', 'Berhasil di Pindah');
    }

    public function godown($id)
    {
        try {

            DB::beginTransaction();
            $data1 = Menu::where('id_menu', $id)->get()->first();
            $data1level = $data1->level;
            $pos1 = $data1->temp;
            $pos2 = $data1->temp + 1;
            $data2 = Menu::where('temp', $pos2)->where('level', $data1level)->get()->first();

            $idnya = $data2->id_menu;

            if (!is_null($data1)) {
                $result = DB::Table('menu')->where('id_menu', $id)->update(
                    array(
                        'temp' => $pos2,
                    )
                );
            }
            if (!is_null($data2)) {
                $result = DB::Table('menu')->where('id_menu', $idnya)->update(
                    array(
                        'temp' => $pos1,
                    )
                );
            }

            DB::commit();
        } catch (Exception $e) {
            return redirect(route_redirect())->with('error', 'Menu gagal di pindah posisi', $e->getMessage());
        }
        return redirect(route_redirect())->with('success', 'Berhasil di Pindah');
    }

    public function temp(Request $request)
    {
        $menu = Menu::with("module")
            ->orderBy("id_module")
            ->orderBy("parent")
            ->orderBy("id_menu")->get();
        $jumlah = count($menu);
        $data = [];

        for ($i = 1; $i <= $jumlah; $i++) {
            $temp = new Menu;
        }

    }

    public function generateTemp()
    {
        $temp1 = [];
        $temp1 = Menu::where("level", "1")->orderBy("id_module")->get();
        foreach ($temp1 as $key => $value) {
            //dd($result);
            $temp = [];
            $temp["temp"] = $key + 1;
            Menu::where("id_menu", $value->id_menu)->update(
                $temp
            );
            $this->lev2($value->id_menu);
        }
        return redirect("menus")->with('success', 'Berhasil Di Generate');
    }

    public function lev2($id)
    {
        $temp = Menu::where("level", "2")->where("parent", $id)->get();
        foreach ($temp as $key => $value) {
            $kunci = $key + 1;
            $temp = [];
            $temp["temp"] = $value->parent . $value->id_module . $value->level . $kunci;
            Menu::where("id_menu", $value->id_menu)->update(
                $temp
            );
            $this->lev3($value->id_menu);
        }
    }

    public function lev3($id)
    {
        $temp = Menu::where("level", "3")->where("parent", $id)->get();
        foreach ($temp as $key => $value) {
            $kunci = $key + 1;
            $temp = [];
            $temp["temp"] = $value->parent . $value->id_module . $value->level . $kunci;
            Menu::where("id_menu", $value->id_menu)->update(
                $temp
            );
        }
    }

}
