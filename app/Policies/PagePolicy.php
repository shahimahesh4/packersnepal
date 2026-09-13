<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->permits('admin.access') && $user->permits('pages.view');
    }

    public function create(User $user): bool
    {
        return $user->permits('admin.access') && $user->permits('pages.create');
    }

    public function view(User $user, Page $page): bool
    {
        return $this->allowed($user, $page, 'view');
    }

    public function update(User $user, Page $page): bool
    {
        return $this->view($user, $page) && $this->allowed($user, $page, 'update');
    }

    public function publish(User $user, Page $page): bool
    {
        return $this->view($user, $page) && $this->allowed($user, $page, 'publish');
    }

    public function delete(User $user, Page $page): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    private function allowed(User $user, Page $page, string $action): bool
    {
        return $user->permits('admin.access') && $user->permits('pages.'.$action)
            && ($user->permits('pages.scope.all') || $page->grants()->where('user_id', $user->id)->where('action', $action)->exists());
    }
}
