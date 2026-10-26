<?php

namespace App\Policies;

use App\Enums\StaffRole;
use App\Models\Site;
use App\Models\User;

class SitePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Site $site): bool
    {
        return $user->canAccessSite($site);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [StaffRole::SuperAdmin, StaffRole::Admin], true);
    }

    public function update(User $user, Site $site): bool
    {
        return in_array($user->role, [StaffRole::SuperAdmin, StaffRole::Admin], true);
    }

    public function rotateKey(User $user, Site $site): bool
    {
        return in_array($user->role, [StaffRole::SuperAdmin, StaffRole::Admin], true);
    }
}
