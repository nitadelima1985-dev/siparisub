<?php

namespace App\Policies;

use App\Enums\RoleCode;
use App\Enums\WorkflowStatus;
use App\Models\Destination;
use App\Models\User;

class DestinationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(
            RoleCode::SuperAdmin,
            RoleCode::AdminDinas,
            RoleCode::AdminPokdarwis,
            RoleCode::AdminHumas,
            RoleCode::ReviewerAkademik,
        );
    }

    public function view(User $user, Destination $destination): bool
    {
        if ($user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas, RoleCode::ReviewerAkademik)) {
            return true;
        }

        return $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas)
            && $destination->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(
            RoleCode::SuperAdmin,
            RoleCode::AdminDinas,
            RoleCode::AdminPokdarwis,
            RoleCode::AdminHumas,
        );
    }

    public function update(User $user, Destination $destination): bool
    {
        if ($destination->workflow_status === WorkflowStatus::RevisionNeeded) {
            return $destination->created_by === $user->id
                && $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas);
        }

        if ($user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)) {
            return true;
        }

        return $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas)
            && $destination->created_by === $user->id
            && $destination->workflow_status === WorkflowStatus::Draft;
    }

    public function delete(User $user, Destination $destination): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas);
    }
    public function submit(User $user, Destination $destination): bool
    {
        if (! in_array($destination->workflow_status, [WorkflowStatus::Draft, WorkflowStatus::RevisionNeeded], true)) {
            return false;
        }

        if ($user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)) {
            return true;
        }

        return $destination->created_by === $user->id
            && $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas);
    }

    public function review(User $user, Destination $destination): bool
    {
        return $user->hasRole(RoleCode::ReviewerAkademik)
            && in_array($destination->workflow_status, [WorkflowStatus::Submitted, WorkflowStatus::UnderReview], true);
    }

    public function approve(User $user, Destination $destination): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && in_array($destination->workflow_status, [WorkflowStatus::Submitted, WorkflowStatus::UnderReview], true);
    }

    public function publish(User $user, Destination $destination): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && $destination->workflow_status === WorkflowStatus::Approved;
    }

    public function archive(User $user, Destination $destination): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && $destination->workflow_status !== WorkflowStatus::Archived;
    }

    public function restoreArchive(User $user, Destination $destination): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && $destination->workflow_status === WorkflowStatus::Archived;
    }
}

