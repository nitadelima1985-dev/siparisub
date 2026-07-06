<?php

namespace App\Models;

use App\Enums\WorkflowStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_id',
        'created_by',
        'updated_by',
        'approved_by',
        'title',
        'slug',
        'short_description',
        'full_description',
        'cover_image',
        'organizer_name',
        'contact_phone',
        'location_name',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'workflow_status',
        'is_active',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'workflow_status' => WorkflowStatus::class,
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('workflow_status', WorkflowStatus::Published)
            ->where('is_active', true)
            ->whereNotNull('published_at');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function latestApproval(): HasOne
    {
        return $this->hasOne(Approval::class, 'approvable_id')
            ->where('approvable_type', $this->getMorphClass())
            ->latestOfMany();
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }
}
