<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WebsiteSetting;

class WebsiteSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->permits('settings.manage');
    }

    public function view(User $user, WebsiteSetting $setting): bool
    {
        return $user->permits('settings.manage');
    }

    public function create(User $user): bool
    {
        return $user->permits('settings.manage') && ! WebsiteSetting::query()->exists();
    }

    public function update(User $user, WebsiteSetting $setting): bool
    {
        return $user->permits('settings.manage');
    }

    public function delete(User $user, WebsiteSetting $setting): bool
    {
        return false;
    }

    public function restore(User $user, WebsiteSetting $setting): bool
    {
        return false;
    }

    public function forceDelete(User $user, WebsiteSetting $setting): bool
    {
        return false;
    }
}
