<?php

namespace Modules\Sdm\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'report_date',
        'role_type',
        'summary',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Relationship with DailyReportItem
     */
    public function items()
    {
        return $this->hasMany(DailyReportItem::class);
    }

    /**
     * Get items by type
     */
    public function getItemsByType($type)
    {
        return $this->items()->where('type', $type)->get();
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStats()
    {
        $items = $this->items;
        
        return [
            'total_items' => $items->count(),
            'completed_items' => $items->where('status', 'completed')->count(),
            'pending_items' => $items->where('status', 'pending')->count(),
            'in_progress_items' => $items->where('status', 'in_progress')->count(),
        ];
    }
}