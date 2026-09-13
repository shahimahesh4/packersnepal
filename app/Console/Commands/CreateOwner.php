<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateOwner extends Command
{
    protected $signature = 'packers:create-owner';

    protected $description = 'Create the first Owner using a private password prompt';

    public function handle(): int
    {
        (new RolePermissionSeeder)->run();
        if (User::role('Owner')->exists()) {
            $this->error('An Owner already exists. This command only provisions the first Owner.');

            return self::FAILURE;
        }
        $data = ['name' => $this->ask('Name'), 'email' => $this->ask('Email'), 'password' => $this->secret('Password (at least 12 characters)')];
        $validation = Validator::make($data, ['name' => 'required|string|max:255', 'email' => 'required|email|max:255|unique:users,email', 'password' => ['required', Password::min(12)]]);
        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }
        DB::transaction(function () use ($data) {
            $user = User::create($data);
            $user->assignRole('Owner');
            DB::table('audit_events')->insert(['actor_id' => $user->id, 'event' => 'owner.provisioned', 'subject_type' => 'user', 'subject_id' => $user->id, 'created_at' => now()]);
        });
        $this->info('Owner created. Sign in at /stnapanel.');

        return self::SUCCESS;
    }
}
