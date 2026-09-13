<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->permits('admin.access');
    }

    public function view(User $user, Task $task): bool
    {
        return $user->permits('tasks.manage') || $task->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->permits('tasks.manage');
    }

    public function update(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->permits('tasks.manage');
    }

    public function deleteAny(User $user): bool
    {
        return $user->permits('tasks.manage');
    }
}
