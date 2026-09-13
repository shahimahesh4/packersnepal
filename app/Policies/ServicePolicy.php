<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->permits('admin.access') && $user->permits('services.manage');
    }

    public function view(User $user, Service $service): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Service $service): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Service $service): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
