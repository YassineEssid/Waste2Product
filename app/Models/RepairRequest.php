<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'waste_item_id',
        'repairer_id',
        'title',
        'description',
        'status',
        'repairer_notes',
        'before_images',
        'after_images',
        'estimated_cost',
        'actual_cost',
        'assigned_at',
        'started_at',
        'completed_at',
        'urgency',
        'budget',
    ];

    protected $casts = [
        'before_images' => 'array',
        'after_images' => 'array',
        'estimated_cost' => 'decimal:2',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'urgency' => 'string',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wasteItem()
    {
        return $this->belongsTo(WasteItem::class);
    }

    public function repairer()
    {
        return $this->belongsTo(User::class, 'repairer_id');
    }

    // Scopes
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForRepairer($query, $repairerId)
    {
        return $query->where('repairer_id', $repairerId);
    }

    // Mutators
    public function assignToRepairer($repairerId)
    {
        $this->update([
            'repairer_id' => $repairerId,
            'status' => 'assigned',
            'assigned_at' => now()
        ]);
    }

    public function startRepair()
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);
    }

    public function completeRepair($actualCost = null, $notes = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'actual_cost' => $actualCost,
            'repairer_notes' => $notes
        ]);
    }
}
