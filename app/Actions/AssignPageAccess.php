<?php

namespace App\Actions;

use App\Models\PageGrant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AssignPageAccess
{
    public function handle(User $actor, User $target, array $data): void
    {
        DB::transaction(function () use ($actor, $target, $data) {
            $target = User::lockForUpdate()->findOrFail($target->id);
            Gate::forUser($actor)->authorize('update', $target);
            Validator::make($data, ['page_id' => 'required|exists:pages,id', 'actions' => 'present|array', 'actions.*' => [Rule::in(['view', 'update', 'publish'])]])->validate();
            $actions = array_values(array_unique($data['actions']));
            if ($actions && ! in_array('view', $actions, true)) {
                $actions[] = 'view';
            }
            $before = PageGrant::where('user_id', $target->id)->where('page_id', $data['page_id'])->pluck('action')->all();
            PageGrant::where('user_id', $target->id)->where('page_id', $data['page_id'])->delete();
            foreach ($actions as $action) {
                PageGrant::create(['user_id' => $target->id, 'page_id' => $data['page_id'], 'action' => $action, 'granted_by' => $actor->id]);
            }
            DB::table('audit_events')->insert(['actor_id' => $actor->id, 'event' => 'page.access.changed', 'subject_type' => 'user', 'subject_id' => $target->id,
                'metadata' => json_encode(['page_id' => $data['page_id'], 'before' => $before, 'after' => $actions]), 'created_at' => now()]);
        });
    }
}
