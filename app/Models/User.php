<?php

namespace App\Models;

use App\Enums\RoleCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'organization_id',
        'phone',
        'organization_name',
        'profile_photo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function createdDestinations(): HasMany
    {
        return $this->hasMany(Destination::class, 'created_by');
    }

    public function updatedDestinations(): HasMany
    {
        return $this->hasMany(Destination::class, 'updated_by');
    }

    public function approvedDestinations(): HasMany
    {
        return $this->hasMany(Destination::class, 'approved_by');
    }

    public function uploadedDestinationMedia(): HasMany
    {
        return $this->hasMany(DestinationMedia::class, 'uploaded_by');
    }

    public function createdArticles(): HasMany
    {
        return $this->hasMany(Article::class, 'created_by');
    }

    public function updatedArticles(): HasMany
    {
        return $this->hasMany(Article::class, 'updated_by');
    }

    public function approvedArticles(): HasMany
    {
        return $this->hasMany(Article::class, 'approved_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function updatedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'updated_by');
    }

    public function approvedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'approved_by');
    }

    public function submittedApprovals(): HasMany
    {
        return $this->hasMany(Approval::class, 'submitted_by');
    }

    public function assignedApprovals(): HasMany
    {
        return $this->hasMany(Approval::class, 'current_reviewer_id');
    }

    public function approvalLogs(): HasMany
    {
        return $this->hasMany(ApprovalLog::class, 'acted_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function hasRole(RoleCode|string ...$roles): bool
    {
        if (! $this->relationLoaded('role')) {
            $this->load('role');
        }

        if (! $this->role) {
            return false;
        }

        $allowedRoles = collect($roles)
            ->map(fn (RoleCode|string $role): string => $role instanceof RoleCode ? $role->value : $role)
            ->all();

        return in_array($this->role->code->value, $allowedRoles, true);
    }

    public function roleCode(): ?RoleCode
    {
        return $this->role?->code;
    }
}
