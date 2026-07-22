<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['department_id', 'name', 'email', 'password', 'role', 'approver_type', 'is_approved', 'approved_at', 'email_verified_at'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'approved_at' => 'datetime', 'is_approved' => 'boolean'];

    public function department() { return $this->belongsTo(Department::class); }


    public function isPendingApproval(): bool
    {
        return !$this->is_approved;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    public function isAssetManagementAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isApprover(): bool
    {
        return $this->role === 'approver';
    }

    public function isRequestor(): bool
    {
        return $this->role === 'requestor';
    }

    public function isFmo(): bool
    {
        return $this->role === 'fmo';
    }

    public function isHousekeeping(): bool
    {
        return $this->role === 'housekeeping';
    }

    public function isDeanApprover(): bool
    {
        return $this->isApprover() && $this->approver_type === 'dean';
    }

    public function isExecutiveApprover(): bool
    {
        return $this->isApprover() && $this->approver_type === 'executive';
    }

    public function isAdviserApprover(): bool
    {
        return $this->isApprover() && $this->approver_type === 'adviser';
    }

    public function isSdaoApprover(): bool
    {
        return $this->isApprover() && $this->approver_type === 'sdao';
    }

    public function isAcademicDirectorApprover(): bool
    {
        return $this->isApprover() && $this->approver_type === 'academic_director';
    }

    public function homeRouteName(): string
    {
        if ($this->isSuperAdmin() || $this->isAssetManagementAdmin()) {
            return 'dashboard';
        }
        if ($this->isFmo()) {
            return 'facilities.index';
        }
        if ($this->isHousekeeping()) {
            return 'asset-scans.index';
        }
        if ($this->isAdviserApprover() || $this->isSdaoApprover() || $this->isAcademicDirectorApprover()) {
            return 'activity-proposals.index';
        }
        if ($this->isDeanApprover() || $this->isExecutiveApprover()) {
            return 'requisitions.index';
        }
        if ($this->isRequestor()) {
            return 'items.index';
        }
        return 'dashboard';
    }

    public function canManageInventory(): bool
    {
        return $this->isAdmin();
    }

    public function canViewReports(): bool
    {
        return $this->isAdmin();
    }

    public function canUseQrScanner(): bool
    {
        return $this->isAdmin() || $this->isHousekeeping();
    }

    public function canManageFacilities(): bool
    {
        return $this->isFmo() || $this->isSuperAdmin();
    }

    public function canHandleAssetScans(): bool
    {
        return $this->isAdmin() || $this->isHousekeeping();
    }
}
