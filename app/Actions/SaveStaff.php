<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class SaveStaff
{
    public const ROLES = ['Website Manager', 'Page Editor', 'Page Publisher', 'Sales Manager'];

    public function handle(User $actor, array $data, ?User $record = null): User
    {
        return DB::transaction(function () use ($actor, $data, $record) {
            if ($record) {
                $record = User::lockForUpdate()->findOrFail($record->id);
            }
            Gate::forUser($actor)->authorize($record ? 'update' : 'create', $record ?? User::class);
            Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($record?->id)],
                'password' => [$record ? 'nullable' : 'required', 'string', 'min:12'],
                'is_active' => 'required|boolean',
                'staff_roles' => 'required|array|min:1',
                'staff_roles.*' => [Rule::exists(Role::class, 'name')->where(fn ($query) => $query->where('guard_name', 'web')->where('name', '!=', 'Owner'))],
            ])->validate();
            $user = $record ?? new User;
            $before = $record ? ['roles' => $record->getRoleNames()->all(), 'active' => $record->is_active] : null;
            $user->fill(['name' => $data['name'], 'email' => $data['email']]);
            if (! empty($data['password'])) {
                $user->password = $data['password'];
            }
            $user->is_active = $data['is_active'];
            $user->save();
            $user->syncRoles($data['staff_roles']);
            DB::table('audit_events')->insert([
                'actor_id' => $actor->id, 'event' => $record ? 'staff.updated' : 'staff.created', 'subject_type' => 'user', 'subject_id' => $user->id,
                'metadata' => json_encode(['before' => $before, 'after' => ['roles' => $data['staff_roles'], 'active' => $user->is_active]]), 'created_at' => now(),
            ]);

            return $user;
        });
    }
}
