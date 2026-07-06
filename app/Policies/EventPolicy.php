<?php

namespace App\Policies;

use App\Enums\RoleCode;
use App\Enums\WorkflowStatus;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(
            RoleCode::SuperAdmin,
            RoleCode::AdminDinas,
            RoleCode::AdminPokdarwis,
            RoleCode::AdminHumas,
            RoleCode::KontenKreator,
            RoleCode::ReviewerAkademik,
        );
    }

    public function view(User $user, Event $event): bool
    {
        if ($user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas, RoleCode::ReviewerAkademik)) {
            return true;
        }

        return $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas, RoleCode::KontenKreator)
            && $event->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(
            RoleCode::SuperAdmin,
            RoleCode::AdminDinas,
            RoleCode::AdminPokdarwis,
            RoleCode::AdminHumas,
            RoleCode::KontenKreator,
        );
    }

    public function update(User $user, Event $event): bool
    {
        if ($event->workflow_status === WorkflowStatus::RevisionNeeded) {
            return $event->created_by === $user->id
                && $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas, RoleCode::KontenKreator);
        }

        if ($user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)) {
            return true;
        }

        return $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas, RoleCode::KontenKreator)
            && $event->created_by === $user->id
            && $event->workflow_status === WorkflowStatus::Draft;
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas);
    }
    public function submit(User $user, Event $event): bool
    {
        if (! in_array($event->workflow_status, [WorkflowStatus::Draft, WorkflowStatus::RevisionNeeded], true)) {
            return false;
        }

        if ($user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)) {
            return true;
        }

        return $event->created_by === $user->id
            && $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas, RoleCode::KontenKreator);
    }

    public function review(User $user, Event $event): bool
    {
        return $user->hasRole(RoleCode::ReviewerAkademik)
            && in_array($event->workflow_status, [WorkflowStatus::Submitted, WorkflowStatus::UnderReview], true);
    }

    public function approve(User $user, Event $event): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && in_array($event->workflow_status, [WorkflowStatus::Submitted, WorkflowStatus::UnderReview], true);
    }

    public function publish(User $user, Event $event): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && $event->workflow_status === WorkflowStatus::Approved;
    }

    public function archive(User $user, Event $event): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && $event->workflow_status !== WorkflowStatus::Archived;
    }

    public function restoreArchive(User $user, Event $event): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && $event->workflow_status === WorkflowStatus::Archived;
    }
}
