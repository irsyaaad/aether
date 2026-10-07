<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Session;

class Menu extends Model
{
    protected $table = "menu";
    protected $primaryKey = 'id_menu';

    public function module()
    {
        return $this->belongsTo('App\Models\Module', 'id_module', 'id_module');
    }

    public function RoleUser()
    {
        return $this->hasMany('App\Models\RoleUser', 'id_menu', 'id_menu');
    }

    public static function getLevel($id_module, $level)
    {
        // dd(Session::all(), Session('role')['id_role']);
        $sql = "SELECT m.*,f.id_role,f.id_rm
		from menu m
		join
		(
			select id_menu,id_role,id_rm from role_menu r where id_role='" . Session("role")["id_role"] . "'
			) as f on m.id_menu=f.id_menu
		where m.id_module='" . $id_module . "' and m.level='" . $level . "' and m.tampil='1' order by m.temp asc";

        $data = DB::select($sql);

        $a_data = [];
        if ($level == 2 or $level == 3) {
            foreach ($data as $key => $value) {
                $a_data[$value->parent][$key] = $value;
            }
        } else {
            $a_data = $data;
        }
        return $a_data;
    }

    public static function getMenu($id_module = null)
    {
        $data = self::join('role_menu', 'menu.id_menu', '=', 'role_menu.id_menu')
            ->select('menu.*', 'role_menu.id_module')
            ->where('id_role', '=', Session('role')['id_role']);

        if (isset($id_module)) {
            $data = $data->where('role_menu.id_module', '=', $id_module);
        }

        return $data;
    }

    public static function getModule($route)
    {
        return self::join('module', 'module.id_module', '=', 'menu.id_module')
            ->join('role_menu', 'menu.id_menu', '=', 'role_menu.id_menu')
            ->where('id_role', '=', Session('role')['id_role'])
            ->where('menu.route', $route)->first();
    }
}
