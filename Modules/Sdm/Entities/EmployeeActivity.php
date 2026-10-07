<?php
namespace Modules\Sdm\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeActivity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'activity_date',
        'start_time',
        'end_time',
        'activity',
        'do_today',
        'do_tomorrow',
        'truck_positions',
        'photo',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function getTimeRangeAttribute()
    {
        return $this->start_time . ' - ' . $this->end_time;
    }
}
