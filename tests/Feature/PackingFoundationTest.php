<?php

namespace Tests\Feature;

use App\Actions\AssignPageAccess;
use App\Actions\PublishPage;
use App\Actions\SaveStaff;
use App\Filament\Resources\Pages\Pages\ManagePages;
use App\Filament\Resources\Roles\Pages\ManageRoles;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Tasks\Pages\ManageTasks;
use App\Filament\Resources\Testimonials\Pages\ManageTestimonials;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Filament\Resources\WebsiteSettings\Pages\ManageWebsiteSettings;
use App\Filament\Widgets\BusinessOverview;
use App\Filament\Widgets\RecentInquiries;
use App\Livewire\ContactRequest;
use App\Livewire\QuoteRequest;
use App\Mail\InquirySubmitted;
use App\Models\Inquiry;
use App\Models\Page;
use App\Models\Service;
use App\Models\Task;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\WebsiteSetting;
use App\Support\AboutPageDefaults;
use App\Support\HomePageDefaults;
use App\Support\ServicePageDefaults;
use Database\Seeders\DatabaseSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PackingFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function staff(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function form(): Testable
    {
        return Livewire::test(QuoteRequest::class)
            ->set('name', 'Sample Customer')->set('email', 'customer@example.test')->set('phone', '+977 9800000000')
            ->set('address', 'Kathmandu, sample packing site')->set('service_id', (string) Service::first()->id)
            ->set('details', 'Pack two rooms and carefully wrap the glassware.')->set('consent', true);
    }

    public function test_public_site_and_packing_form_render(): void
    {
        $this->get('/')->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff')->assertHeader('X-Frame-Options', 'SAMEORIGIN')->assertSee('Move forward')->assertSee('Transportation arranged by you')->assertSee('Packers Nepal on Facebook')->assertSee('info@packersnepal.com')->assertDontSee('Staff login')
            ->assertSee('wire:navigate href="'.route('home').'"', false)
            ->assertSee('<meta name="robots" content="index, follow, max-image-preview:large">', false)
            ->assertSee('<link rel="canonical" href="'.url('/').'">', false)
            ->assertSee('property="og:locale" content="en_NP"', false)
            ->assertSee('name="twitter:card" content="summary_large_image"', false)
            ->assertSee('"@type":"WebSite"', false)
            ->assertSee('"@type":["LocalBusiness","ProfessionalService"]', false)
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('packing-team-hero.jpg');
        $this->get('/request-quote')->assertOk()->assertSee('Your packing details');
        $this->get('/contact')->assertOk()->assertSee('How can we help?')->assertSee('New Road, Kathmandu')->assertSee('9801010000')->assertSee('info@packersnepal.com');
        $this->get('/pages/about')
            ->assertOk()
            ->assertSee('About Our Professional Packing Services in Nepal')
            ->assertSee('Packing Is Our Speciality')
            ->assertSee('Kathmandu Valley’s careful packing specialists', false);
        $this->get('/pages/how-it-works')
            ->assertOk()
            ->assertSee('From your request to a')
            ->assertSee('What you arrange')
            ->assertSee('does not provide transportation');
        $this->get('/stnapanel/login')->assertOk();
    }

    public function test_xml_sitemap_contains_all_indexable_content(): void
    {
        $service = Service::query()->where('is_active', true)->firstOrFail();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('home'))
            ->assertSee(route('services.show', $service))
            ->assertSee(route('page.show', 'about'))
            ->assertDontSee('/stnapanel');
    }

    public function test_advanced_page_seo_controls_render_and_noindex_content_is_excluded_from_sitemap(): void
    {
        $page = Page::query()->where('slug', 'about')->firstOrFail();
        $page->forceFill([
            'published_canonical_url' => 'https://packersnepal.com/about-packers-nepal',
            'published_robots_index' => false,
            'published_robots_follow' => false,
            'published_social_title' => 'Packers Nepal social title',
            'published_social_description' => 'A dedicated description for social sharing.',
        ])->save();

        $this->get('/pages/about')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="https://packersnepal.com/about-packers-nepal">', false)
            ->assertSee('<meta name="robots" content="noindex, nofollow, max-image-preview:large">', false)
            ->assertSee('<meta property="og:title" content="Packers Nepal social title">', false);

        $this->get('/sitemap.xml')->assertDontSee(route('page.show', 'about'));
    }

    public function test_quote_request_is_saved_once_and_acknowledged(): void
    {
        Mail::fake();

        $form = $this->form()->call('submit')->assertHasNoErrors()->assertSee('Your request is with us.');
        $form->call('submit');
        $this->assertDatabaseCount('inquiries', 1);
        $this->assertNotNull(Inquiry::first()->consented_at);
        Mail::assertSent(InquirySubmitted::class, 2);
        Mail::assertSent(InquirySubmitted::class, fn (InquirySubmitted $mail) => ! $mail->staffCopy && $mail->hasTo('customer@example.test'));
        Mail::assertSent(InquirySubmitted::class, fn (InquirySubmitted $mail) => $mail->staffCopy && $mail->hasTo('info@packersnepal.com'));
    }

    public function test_contact_message_is_stored_for_staff_review(): void
    {
        Mail::fake();

        Livewire::test(ContactRequest::class)
            ->set('name', 'Contact Customer')
            ->set('email', 'contact@example.test')
            ->set('phone', '+977 9800000000')
            ->set('subject', 'Question about packing materials')
            ->set('message', 'Please tell me which materials you use for fragile items.')
            ->set('consent', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSee('Thank you for contacting us.');

        $this->assertDatabaseHas('inquiries', [
            'inquiry_type' => 'contact',
            'subject' => 'Question about packing materials',
            'service_id' => null,
        ]);
        Mail::assertSent(InquirySubmitted::class, 2);
        Mail::assertSent(InquirySubmitted::class, fn (InquirySubmitted $mail) => ! $mail->staffCopy && $mail->hasTo('contact@example.test'));
        Mail::assertSent(InquirySubmitted::class, fn (InquirySubmitted $mail) => $mail->staffCopy && $mail->hasTo('info@packersnepal.com'));
    }

    public function test_website_settings_control_public_contact_and_footer_content(): void
    {
        $homeContent = HomePageDefaults::content();
        $homeContent['services_title'] = 'Services managed from the homepage settings';
        $homeVisibility = HomePageDefaults::visibility();
        $homeVisibility['faq'] = false;

        WebsiteSetting::firstOrFail()->update([
            'address' => 'Lalitpur Service Office',
            'phone' => '01-555-0101',
            'email' => 'hello@example.test',
            'business_description' => 'Careful packing managed by our specialists.',
            'home_banner_title' => 'Packing planned around you.',
            'home_banner_accent' => 'Handled with care.',
            'home_content' => $homeContent,
            'home_visibility' => $homeVisibility,
        ]);

        $this->get('/contact')->assertOk()
            ->assertSee('Lalitpur Service Office')
            ->assertSee('01-555-0101')
            ->assertSee('hello@example.test');

        $this->get('/')->assertOk()
            ->assertSee('Careful packing managed by our specialists.')
            ->assertSee('Packing planned around you.')
            ->assertSee('Handled with care.')
            ->assertSee('Services managed from the homepage settings')
            ->assertDontSee('Frequently asked questions');
    }

    public function test_website_manager_can_manage_settings_but_sales_manager_cannot(): void
    {
        $this->actingAs($this->staff('Website Manager'))
            ->get('/stnapanel/website-settings')
            ->assertOk();

        $settings = WebsiteSetting::firstOrFail();
        Livewire::test(ManageWebsiteSettings::class)
            ->callAction(TestAction::make('edit')->table($settings), array_merge($settings->only($settings->getFillable()), [
                'tagline' => 'Packed with professional care',
            ]))
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('website_settings', ['tagline' => 'Packed with professional care']);

        auth()->logout();

        $this->actingAs($this->staff('Sales Manager'))
            ->get('/stnapanel/website-settings')
            ->assertForbidden();
    }

    public function test_owner_dashboard_shows_business_overview_and_recent_requests(): void
    {
        $this->form()->call('submit')->assertHasNoErrors();

        $this->actingAs($this->staff('Owner'));

        Livewire::test(BusinessOverview::class)
            ->assertSee('Packers Nepal at a glance')
            ->assertSee('New requests');

        Livewire::test(RecentInquiries::class)
            ->assertSee('Recent quotes and messages')
            ->assertSee(Inquiry::firstOrFail()->reference);
    }

    public function test_active_services_have_public_detail_pages(): void
    {
        foreach (Service::where('is_active', true)->get() as $service) {
            $this->get(route('services.show', $service))
                ->assertOk()
                ->assertSee($service->name)
                ->assertSee('What to expect');
        }

        $inactive = Service::first();
        $pageContent = ServicePageDefaults::content();
        $pageContent['banner_title'] = 'A database-managed service heading';
        $visibility = ServicePageDefaults::visibility();
        $visibility['expectations'] = false;
        $inactive->update(['page_content' => $pageContent, 'section_visibility' => $visibility]);
        $this->get(route('services.show', $inactive))->assertOk()
            ->assertSee('A database-managed service heading')
            ->assertDontSee('What to expect');

        $inactive->update(['is_active' => false]);
        $this->get(route('services.show', $inactive))->assertNotFound();
    }

    public function test_testimonials_are_dynamic_public_content_with_separate_admin_menu(): void
    {
        $testimonial = Testimonial::firstOrFail();

        $this->get('/testimonials')->assertOk()
            ->assertSee('Care people can')
            ->assertSee($testimonial->customer_name)
            ->assertSee($testimonial->review);

        $this->get('/')->assertOk()->assertSee($testimonial->review);

        $testimonial->update(['is_active' => false]);
        $this->get('/testimonials')->assertDontSee($testimonial->review);

        $this->actingAs($this->staff('Website Manager'));
        $this->get('/stnapanel/testimonials')->assertOk();
        Livewire::test(ManageTestimonials::class)->assertSee('Add testimonial');
    }

    public function test_quote_rejects_invalid_service_past_date_and_missing_consent(): void
    {
        $this->form()->set('service_id', '999')->set('preferred_date', '2020-01-01')->set('consent', false)
            ->call('submit')->assertHasErrors(['service_id', 'preferred_date', 'consent']);
        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_inactive_service_cannot_be_requested(): void
    {
        Service::first()->update(['is_active' => false]);
        $this->form()->call('submit')->assertHasErrors('service_id');
    }

    public function test_spam_throttle_blocks_write(): void
    {
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit('quote-request:127.0.0.1', 60);
        }
        $this->form()->call('submit')->assertHasErrors('name');
        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_guest_cannot_open_admin_resources(): void
    {
        $this->get('/stnapanel/pages')->assertRedirect('/stnapanel/login');
        $this->get('/stnapanel/inquiries')->assertRedirect('/stnapanel/login');
    }

    public function test_editor_only_sees_and_edits_assigned_page(): void
    {
        $editor = $this->staff('Page Editor');
        $owner = $this->staff('Owner');
        $page = Page::first();
        app(AssignPageAccess::class)->handle($owner, $editor, ['page_id' => $page->id, 'actions' => ['view', 'update']]);
        $other = Page::where('id', '!=', $page->id)->first();
        $this->actingAs($editor);
        Livewire::test(ManagePages::class)->assertCanSeeTableRecords([$page])->assertCanNotSeeTableRecords([$other])
            ->callAction(TestAction::make('edit')->table($page), ['title' => 'New draft', 'content' => 'Private draft content'])
            ->assertHasNoActionErrors();
        $this->assertSame('New draft', $page->fresh()->title);
        $this->assertFalse(Gate::forUser($editor)->allows('update', $other));
        $this->assertFalse(Gate::forUser($editor)->allows('publish', $page));
    }

    public function test_publish_grant_cannot_override_editor_role(): void
    {
        $editor = $this->staff('Page Editor');
        $owner = $this->staff('Owner');
        $page = Page::first();
        app(AssignPageAccess::class)->handle($owner, $editor, ['page_id' => $page->id, 'actions' => ['view', 'publish']]);
        $this->expectException(AuthorizationException::class);
        app(PublishPage::class)->handle($editor, $page);
    }

    public function test_public_page_keeps_published_copy_until_authorized_publish(): void
    {
        $page = Page::first();
        $page->update(['content' => 'New unpublished content']);
        $this->get('/pages/'.$page->slug)->assertDontSee('New unpublished content');
        $publisher = $this->staff('Page Publisher');
        app(AssignPageAccess::class)->handle($this->staff('Owner'), $publisher, ['page_id' => $page->id, 'actions' => ['view', 'publish']]);
        $this->actingAs($publisher);
        Livewire::test(ManagePages::class)->callAction(TestAction::make('publish')->table($page))->assertHasNoActionErrors();
        $this->get('/pages/'.$page->slug)->assertSee('New unpublished content');
        $this->assertDatabaseCount('page_revisions', 1);
        $this->assertDatabaseHas('audit_events', ['event' => 'page.published']);
    }

    public function test_about_banner_is_page_specific_and_uses_publish_workflow(): void
    {
        $page = Page::where('slug', 'about')->firstOrFail();
        $aboutContent = AboutPageDefaults::content();
        $aboutContent['company_title'] = 'A fully managed company story';
        $aboutContent['cta_title'] = 'A dynamic final invitation';
        $page->update([
            'banner_title' => 'A new banner draft',
            'banner_accent' => 'Prepared with purpose.',
            'banner_description' => 'This banner description is controlled from the About Us page record.',
            'show_standards' => false,
            'about_content' => $aboutContent,
        ]);

        $this->get('/pages/about')->assertDontSee('A new banner draft')->assertDontSee('A fully managed company story')->assertSee('What guides us');

        app(PublishPage::class)->handle($this->staff('Owner'), $page);

        $this->get('/pages/about')->assertOk()
            ->assertSee('A new banner draft')
            ->assertSee('Prepared with purpose.')
            ->assertSee('This banner description is controlled from the About Us page record.')
            ->assertSee('A fully managed company story')
            ->assertSee('A dynamic final invitation')
            ->assertDontSee('What guides us');
    }

    public function test_unpublished_pages_are_private_and_html_is_escaped(): void
    {
        $page = Page::create(['title' => 'Draft', 'slug' => 'draft', 'content' => '<script>alert(1)</script>']);
        $this->get('/pages/draft')->assertNotFound();
        app(PublishPage::class)->handle($this->staff('Owner'), $page);
        $this->get('/pages/draft')->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
    }

    public function test_website_manager_cannot_read_inquiries_or_manage_staff(): void
    {
        $manager = $this->staff('Website Manager');
        $this->actingAs($manager)->get('/stnapanel/inquiries')->assertForbidden();
        $this->get('/stnapanel/users')->assertForbidden();
        $this->get('/stnapanel/pages')->assertOk();
        $this->get('/stnapanel/services')->assertOk();
    }

    public function test_suspended_owner_is_blocked(): void
    {
        $owner = $this->staff('Owner');
        $owner->forceFill(['is_active' => false])->save();
        $this->actingAs($owner)->get('/stnapanel')->assertForbidden();
        $this->assertFalse(Gate::forUser($owner)->allows('publish', Page::first()));
    }

    public function test_revoked_page_access_takes_effect_immediately(): void
    {
        $owner = $this->staff('Owner');
        $editor = $this->staff('Page Editor');
        $page = Page::first();
        app(AssignPageAccess::class)->handle($owner, $editor, ['page_id' => $page->id, 'actions' => ['update']]);
        $this->assertTrue(Gate::forUser($editor)->allows('update', $page));
        app(AssignPageAccess::class)->handle($owner, $editor, ['page_id' => $page->id, 'actions' => []]);
        $this->assertFalse(Gate::forUser($editor)->allows('update', $page));
    }

    public function test_owner_can_create_staff_from_filament(): void
    {
        $this->actingAs($this->staff('Owner'));
        Livewire::test(ManageUsers::class)->callAction('create', ['name' => 'Page colleague', 'email' => 'editor@example.test', 'password' => 'local-only-test-password', 'is_active' => true, 'staff_roles' => ['Page Editor']])->assertHasNoActionErrors();
        $this->assertTrue(User::where('email', 'editor@example.test')->firstOrFail()->hasRole('Page Editor'));
    }

    public function test_owner_can_create_roles_and_assign_them_to_new_users(): void
    {
        $this->actingAs($this->staff('Owner'));
        $permissionIds = Permission::query()->whereIn('name', ['admin.access', 'pages.view'])->pluck('id')->all();

        Livewire::test(ManageRoles::class)
            ->callAction(TestAction::make('create'), [
                'name' => 'Content Reviewer',
                'guard_name' => 'web',
                'permissions' => $permissionIds,
            ])
            ->assertHasNoActionErrors();

        $role = Role::findByName('Content Reviewer');
        $this->assertTrue($role->hasAllPermissions(['admin.access', 'pages.view']));

        Livewire::test(ManageUsers::class)
            ->callAction('create', [
                'name' => 'Review User',
                'email' => 'reviewer@example.test',
                'password' => 'local-only-test-password',
                'is_active' => true,
                'staff_roles' => ['Content Reviewer'],
            ])
            ->assertHasNoActionErrors();

        $this->assertTrue(User::where('email', 'reviewer@example.test')->firstOrFail()->hasRole('Content Reviewer'));
    }

    public function test_only_owner_can_manage_roles_and_owner_role_is_protected(): void
    {
        $manager = $this->staff('Website Manager');
        $this->actingAs($manager)->get('/stnapanel/roles')->assertForbidden();

        $this->actingAs($this->staff('Owner'));
        $this->get('/stnapanel/roles')->assertOk();

        $ownerRole = Role::findByName('Owner');
        $this->assertFalse(RoleResource::canEdit($ownerRole));
        $this->assertFalse(RoleResource::canDelete($ownerRole));
    }

    public function test_super_admin_and_admin_roles_have_the_expected_access(): void
    {
        $superAdmin = $this->staff('Super Admin');
        $admin = $this->staff('Admin');

        $this->assertTrue($superAdmin->permits('users.manage'));
        $this->assertTrue($superAdmin->permits('roles.manage'));
        $this->assertTrue($admin->permits('tasks.manage'));
        $this->assertFalse($admin->permits('users.manage'));
        $this->assertFalse($admin->permits('roles.manage'));

        $this->actingAs($superAdmin)->get('/stnapanel/roles')->assertOk();
        $this->actingAs($admin)->get('/stnapanel/roles')->assertForbidden();
    }

    public function test_admin_can_assign_a_task_and_assignee_can_access_it(): void
    {
        $admin = $this->staff('Admin');
        $editor = $this->staff('Page Editor');
        $page = Page::firstOrFail();

        $this->actingAs($admin);
        Livewire::test(ManageTasks::class)
            ->callAction('create', [
                'title' => 'Review About page copy',
                'description' => 'Review the page wording before it is published.',
                'assigned_to' => $editor->id,
                'page_id' => $page->id,
                'priority' => 'high',
                'status' => 'pending',
            ])
            ->assertHasNoActionErrors();

        $task = Task::where('title', 'Review About page copy')->firstOrFail();
        $this->assertSame($admin->id, $task->created_by);
        $this->assertSame($editor->id, $task->assigned_to);

        $this->actingAs($editor)->get('/stnapanel/tasks')->assertOk();
        $this->assertTrue(Gate::forUser($editor)->allows('update', $task));
        $this->assertFalse(Gate::forUser($editor)->allows('delete', $task));
    }

    public function test_owner_role_cannot_be_injected_into_staff_form(): void
    {
        $this->expectException(ValidationException::class);
        app(SaveStaff::class)->handle($this->staff('Owner'), ['name' => 'Bad grant', 'email' => 'bad@example.test', 'password' => 'local-only-test-password', 'is_active' => true, 'staff_roles' => ['Owner']]);
    }

    public function test_owner_assigns_page_access_from_filament_and_updates_staff_without_password_change(): void
    {
        $owner = $this->staff('Owner');
        $editor = $this->staff('Page Editor');
        $hash = $editor->password;
        $page = Page::first();
        $this->actingAs($owner);
        Livewire::test(ManageUsers::class)
            ->callAction(TestAction::make('pageAccess')->table($editor), ['page_id' => $page->id, 'actions' => ['update']])
            ->assertHasNoActionErrors()
            ->callAction(TestAction::make('edit')->table($editor), ['name' => 'Updated editor', 'email' => $editor->email, 'password' => '', 'is_active' => true, 'staff_roles' => ['Page Editor']])
            ->assertHasNoActionErrors();
        $this->assertTrue(Gate::forUser($editor)->allows('update', $page));
        $this->assertSame($hash, $editor->fresh()->password);
        $this->assertDatabaseHas('audit_events', ['event' => 'page.access.changed']);
    }

    public function test_editor_cannot_mount_edit_or_publish_for_an_unassigned_page(): void
    {
        $this->actingAs($this->staff('Page Editor'));
        Livewire::test(ManagePages::class)
            ->assertActionDoesNotExist(TestAction::make('edit')->table(Page::first()))
            ->assertActionDoesNotExist(TestAction::make('publish')->table(Page::first()));
    }

    public function test_owner_cannot_modify_self_through_staff_action(): void
    {
        $owner = $this->staff('Owner');
        $this->expectException(AuthorizationException::class);
        app(SaveStaff::class)->handle($owner, [], $owner);
    }

    public function test_seeder_is_repeatable_and_never_creates_default_accounts(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('services', 4);
        $this->assertDatabaseCount('pages', 2);
    }
}
