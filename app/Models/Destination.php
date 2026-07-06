<?php

namespace App\Models;

use App\Enums\WorkflowStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_category_id',
        'district_id',
        'created_by',
        'updated_by',
        'approved_by',
        'name',
        'slug',
        'short_description',
        'full_description',
        'address',
        'village_name',
        'latitude',
        'longitude',
        'google_maps_url',
        'open_days',
        'open_hours',
        'ticket_adult',
        'ticket_child',
        'parking_fee',
        'contact_phone',
        'contact_email',
        'website_url',
        'instagram_url',
        'tiktok_url',
        'main_attraction',
        'activities',
        'best_visit_time',
        'access_notes',
        'workflow_status',
        'is_featured',
        'is_active',
        'published_at',
        'last_content_update_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'ticket_adult' => 'decimal:2',
            'ticket_child' => 'decimal:2',
            'parking_fee' => 'decimal:2',
            'workflow_status' => WorkflowStatus::class,
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'published_at' => 'datetime',
            'last_content_update_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('workflow_status', WorkflowStatus::Published)
            ->where('is_active', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DestinationCategory::class, 'destination_category_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
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

    public function media(): HasMany
    {
        return $this->hasMany(DestinationMedia::class);
    }

    public function coverMedia(): HasOne
    {
        return $this->hasOne(DestinationMedia::class)->where('is_cover', true)->latestOfMany();
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
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


