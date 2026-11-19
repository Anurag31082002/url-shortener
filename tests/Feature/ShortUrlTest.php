<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_homepage_opens_the_separate_login_screen(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));

        $this->get('/login')
            ->assertOk()
            ->assertSee('Log in')
            ->assertSee('URL Shortener');
    }

    public function test_database_seeder_creates_demo_accounts_and_sample_links(): void
    {
        $this->seed();

        foreach ([
            'superadmin@example.com' => 'SuperAdmin',
            'admin@example.com' => 'Admin',
            'member@example.com' => 'Member',
            'sales@example.com' => 'Sales',
            'manager@example.com' => 'Manager',
        ] as $email => $role) {
            $this->assertDatabaseHas('users', [
                'email' => $email,
                'role' => $role,
            ]);
        }

        $this->assertDatabaseCount('short_urls', 3);
    }

    public function test_super_admin_login_lands_on_dashboard_and_can_view_all_short_urls(): void
    {
        $company = $this->createCompany('team@example.com');
        $creator = User::factory()->create(['role' => 'Member', 'company_id' => $company->id]);
        $this->createLink($creator, 'clientlink1', $company);
        $superAdmin = User::factory()->create([
            'email' => 'platform@example.com',
            'password' => 'password',
            'role' => 'SuperAdmin',
        ]);

        $this->post('/login', [
            'email' => $superAdmin->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->get('/dashboard')
            ->assertRedirect(route('superadmin.dashboard'));

        $this->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertSee('Invite a new client')
            ->assertSee('All short URLs')
            ->assertSee('clientlink1')
            ->assertDontSee('name="long_url"');
    }

    public function test_admin_and_super_admin_cannot_create_short_urls(): void
    {
        $company = $this->createCompany('team@example.com');

        foreach (['Admin', 'SuperAdmin'] as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'company_id' => $role === 'SuperAdmin' ? null : $company->id,
            ]);

            $this->actingAs($user)
                ->post(route('short-urls.store'), [
                    'long_url' => 'https://example.com/destination',
                ])
                ->assertForbidden();
        }

        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_member_can_create_a_company_url_and_only_sees_their_own_urls(): void
    {
        $company = $this->createCompany('team@example.com');
        $member = User::factory()->create([
            'role' => 'Member',
            'company_id' => $company->id,
        ]);
        $otherMember = User::factory()->create([
            'role' => 'Member',
            'company_id' => $company->id,
        ]);
        $this->createLink($otherMember, 'otherlink1', $company);

        $this->actingAs($member)
            ->post(route('short-urls.store'), [
                'long_url' => 'https://google.com/search?q=short+urls',
            ])
            ->assertRedirect(route('member.dashboard'));

        $created = ShortUrl::where('user_id', $member->id)->firstOrFail();
        $this->assertSame($company->id, $created->company_id);

        $this->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee($created->code)
            ->assertDontSee('otherlink1');
    }

    public function test_public_short_url_redirects_and_increments_hits(): void
    {
        $company = $this->createCompany('team@example.com');
        $member = User::factory()->create([
            'role' => 'Member',
            'company_id' => $company->id,
        ]);
        $shortUrl = $this->createLink($member, 'public001', $company);

        $this->get(route('short-urls.redirect', $shortUrl->code))
            ->assertRedirect('https://example.com/public001');

        $this->assertSame(1, $shortUrl->fresh()->hits);
    }

    public function test_admin_sees_all_links_from_their_company_but_not_other_companies(): void
    {
        $company = $this->createCompany('team@example.com');
        $otherCompany = $this->createCompany('other@example.com');
        $admin = User::factory()->create(['role' => 'Admin', 'company_id' => $company->id]);
        $member = User::factory()->create(['role' => 'Member', 'company_id' => $company->id]);
        $otherMember = User::factory()->create(['role' => 'Member', 'company_id' => $otherCompany->id]);

        $this->createLink($member, 'company001', $company);
        $this->createLink($otherMember, 'outside001', $otherCompany);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('company001')
            ->assertDontSee('outside001')
            ->assertDontSee('name="long_url"');
    }

    public function test_super_admin_invitation_creates_company_and_admin_account(): void
    {
        $superAdmin = User::factory()->create(['role' => 'SuperAdmin']);

        $this->actingAs($superAdmin)
            ->post(route('superadmin.invitations.store'), [
                'company_name' => 'Sembark Tech',
                'email' => 'ravi@sembark.com',
            ])
            ->assertRedirect(route('superadmin.dashboard'))
            ->assertSessionHas('invite_url');

        $company = Company::where('name', 'Sembark Tech')->firstOrFail();
        $invitation = Invitation::where('email', 'ravi@sembark.com')->firstOrFail();
        $this->assertSame('Admin', $invitation->role);
        $this->assertSame($company->id, $invitation->company_id);

        $this->post('/logout');
        $this->get(route('invitation.show', $invitation->token))
            ->assertOk()
            ->assertSee('Join Sembark Tech');

        $this->post(route('invitation.accept', $invitation->token), [
            'name' => 'Ravi',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs(User::where('email', 'ravi@sembark.com')->firstOrFail());
        $this->assertDatabaseHas('users', [
            'name' => 'Ravi',
            'email' => 'ravi@sembark.com',
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);
        $this->assertNotNull($invitation->fresh()->accepted_at);

        $this->post('/logout');
        $this->post(route('invitation.accept', $invitation->token), [
            'name' => 'Ravi',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertNotFound();
    }

    public function test_admin_invites_admin_or_member_to_their_own_company_only(): void
    {
        $company = $this->createCompany('team@example.com');
        $otherCompany = $this->createCompany('other@example.com');
        $admin = User::factory()->create(['role' => 'Admin', 'company_id' => $company->id]);

        foreach (['Admin', 'Member'] as $index => $role) {
            $this->actingAs($admin)
                ->post(route('admin.invitations.store'), [
                    'name' => 'Invitee '.$role,
                    'email' => strtolower($role).'@example.com',
                    'role' => $role,
                    'company_id' => $otherCompany->id,
                ])
                ->assertRedirect(route('admin.dashboard'))
                ->assertSessionHas('invite_url');
        }

        $this->assertDatabaseHas('invitations', [
            'email' => 'admin@example.com',
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);
        $this->assertDatabaseHas('invitations', [
            'email' => 'member@example.com',
            'role' => 'Member',
            'company_id' => $company->id,
        ]);
        $this->assertDatabaseMissing('invitations', [
            'company_id' => $otherCompany->id,
        ]);
    }

    private function createCompany(string $email): Company
    {
        return Company::create([
            'name' => 'Workspace '.str_replace('@example.com', '', $email),
            'email' => $email,
        ]);
    }

    private function createLink(User $user, string $code, Company $company): ShortUrl
    {
        return ShortUrl::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'code' => $code,
            'long_url' => 'https://example.com/'.$code,
        ]);
    }
}
