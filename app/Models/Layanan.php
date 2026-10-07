<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = "m_layanan";
	protected $dates = ['deleted_at'];
	protected $primaryKey = 'id_layanan';
	
	public static function getLayanan()
	{
		$data = self::select("id_layanan", "nm_layanan")->get();
		
		return $data;
	}

	public static function getOrderId()
	{
		$data = self::select("id_layanan", "nm_layanan", "kode_layanan")->get();
		$a_data = [];
		foreach($data as $key => $value){
			$a_data[$value->id_layanan] = $value;
		}
		
		return $a_data;
	}

}
