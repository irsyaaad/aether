<?php
namespace Modules\Sdm\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_date',
        'tomorrow_plan',
        'truck_positions',
    ];

    protected $casts = [
        'plan_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
