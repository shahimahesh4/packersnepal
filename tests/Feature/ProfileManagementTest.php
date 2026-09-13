<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_super_admin_can_open_and_update_own_profile(): void
    {
        $user = $this->staff('Super Admin');

        $this->actingAs($user)
            ->get('/stnapanel/profile')
            ->assertOk()
            ->assertSee('Save changes');

        Livewire::test(EditProfile::class)
            ->fillForm([
                'name' => 'Updated Super Admin',
                'email' => $user->email,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Updated Super Admin', $user->fresh()->name);
    }

    public function test_backend_header_shows_visit_website_before_user_menu(): void
    {
        $user = $this->staff('Super Admin');

        $response = $this->actingAs($user)->get('/stnapanel');

        $response->assertOk()
            ->assertSee('Visit website')
            ->assertSee('packers-nepal-logo-admin.png')
            ->assertSee('href="'.route('home').'"', false)
            ->assertSee('target="_blank"', false);

        $html = $response->getContent();

        $this->assertLessThan(
            strpos($html, 'aria-label="User menu"'),
            strpos($html, 'Visit website'),
        );
    }

    public function test_regular_user_can_update_profile_and_password_securely(): void
    {
        $user = $this->staff('Page Editor', 'current-secure-password');

        $this->actingAs($user);

        Livewire::test(EditProfile::class)
            ->fillForm([
                'name' => 'Updated Editor',
                'email' => $user->email,
                'password' => 'replacement-secure-password',
                'passwordConfirmation' => 'replacement-secure-password',
                'currentPassword' => 'current-secure-password',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $user->refresh();

        $this->assertSame('Updated Editor', $user->name);
        $this->assertTrue(Hash::check('replacement-secure-password', $user->password));
    }

    private function staff(string $role, string $password = 'local-only-test-password'): User
    {
        $user = User::factory()->create(['password' => $password]);
        $user->assignRole($role);

        return $user;
    }
}
