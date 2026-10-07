<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoleMenu;
use App\Models\Menu;

class BaseController extends Controller
{
	public $action_role;
	
    public function __construct() {
    	$act_menu = \App\Modules\Menu::where("route", Request::segment(1))->first();
		$this->action_role = \App\Models\RoleMenu::where("id_menu", $act_menu->id_menu)->first();

        \View::share('template.action', $this->action_role);
    }
}
