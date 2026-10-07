<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mutasi extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'tanggal', 'jam', 'keterangan', 'mutasi', 'jenis'];
    protected $table = 'mutasi';
    protected $primaryKey = 'id';
    public $incrementing = false;

    public static function getDataDay($jenis, $startDate, $endDate)
    {
        $data = self::selectRaw('DAY(tanggal) AS hari, SUM(mutasi) AS total');
        if ($jenis == 100) {
            $data = $data->where('jenis', '>', 1);
            $data = $data->where('jenis', '<', 99);
        } else {
            $data = $data->where('jenis', $jenis);
        }
        $data = $data->whereBetween('tanggal', [$startDate, $endDate])
            ->groupByRaw('DAY(tanggal)')
            ->get();

        return $data;
    }

    public static function getDataMonth($jenis, $startDate, $endDate)
    {
        $data = self::selectRaw('MONTH(tanggal) AS bulan, SUM(mutasi) AS total');
        if ($jenis == 100) {
            $data = $data->where('jenis', '>', 1);
            $data = $data->where('jenis', '<', 99);
        } else {
            $data = $data->where('jenis', $jenis);
        }
        $data = $data->whereBetween('tanggal', [$startDate, $endDate])
            ->groupByRaw('MONTH(tanggal)')
            ->get();

        return $data;
    }

    public function type()
    {
        return $this->belongsTo('Modules\Mutasi\Entities\MasterTipeMutasi', 'jenis', 'id');
    } 

}
