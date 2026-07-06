<?php

namespace App\Policies;

use App\Enums\RoleCode;
use App\Enums\WorkflowStatus;
use App\Models\Article;
use App\Models\User;

class ArticlePolicy
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

    public function view(User $user, Article $article): bool
    {
        if ($user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas, RoleCode::ReviewerAkademik)) {
            return true;
        }

        return $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas, RoleCode::KontenKreator)
            && $article->created_by === $user->id;
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

    public function update(User $user, Article $article): bool
    {
        if ($article->workflow_status === WorkflowStatus::RevisionNeeded) {
            return $article->created_by === $user->id
                && $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas, RoleCode::KontenKreator);
        }

        if ($user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)) {
            return true;
        }

        return $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas, RoleCode::KontenKreator)
            && $article->created_by === $user->id
            && $article->workflow_status === WorkflowStatus::Draft;
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas);
    }

    public function submit(User $user, Article $article): bool
    {
        if (! in_array($article->workflow_status, [WorkflowStatus::Draft, WorkflowStatus::RevisionNeeded], true)) {
            return false;
        }

        if ($user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)) {
            return true;
        }

        return $article->created_by === $user->id
            && $user->hasRole(RoleCode::AdminPokdarwis, RoleCode::AdminHumas, RoleCode::KontenKreator);
    }

    public function review(User $user, Article $article): bool
    {
        return $user->hasRole(RoleCode::ReviewerAkademik)
            && in_array($article->workflow_status, [WorkflowStatus::Submitted, WorkflowStatus::UnderReview], true);
    }

    public function approve(User $user, Article $article): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && in_array($article->workflow_status, [WorkflowStatus::Submitted, WorkflowStatus::UnderReview], true);
    }

    public function publish(User $user, Article $article): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && $article->workflow_status === WorkflowStatus::Approved;
    }

    public function archive(User $user, Article $article): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && $article->workflow_status !== WorkflowStatus::Archived;
    }

    public function restoreArchive(User $user, Article $article): bool
    {
        return $user->hasRole(RoleCode::SuperAdmin, RoleCode::AdminDinas)
            && $article->workflow_status === WorkflowStatus::Archived;
    }
}
