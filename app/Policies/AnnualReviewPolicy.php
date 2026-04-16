<?php

namespace App\Policies;

use App\Models\AnnualReview;
use App\Models\User;

class AnnualReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AnnualReview $review): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $review->employee_id === $user->id
            || $review->manager_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function employeeEdit(User $user, AnnualReview $review): bool
    {
        return $review->employee_id === $user->id
            && $review->isEditableByEmployee();
    }

    public function managerEdit(User $user, AnnualReview $review): bool
    {
        if ($user->isAdmin()) {
            return $review->isEditableByManager();
        }
        return $review->manager_id === $user->id
            && $review->isEditableByManager();
    }

    public function delete(User $user, AnnualReview $review): bool
    {
        return $user->isAdmin() && ! $review->isSigned();
    }
}
