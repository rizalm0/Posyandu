<?php

namespace Tests\Feature;

use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_matching_login_page(): void
    {
        $this->get('/kader')->assertRedirect(route('kader.login'));
        $this->get('/kader/balita')->assertRedirect(route('kader.login'));
        $this->get('/orang-tua')->assertRedirect(route('orang-tua.login'));
        $this->get('/orang-tua/anak/1/grafik')->assertRedirect(route('orang-tua.login'));
    }

    public function test_kader_can_login_with_email_and_is_redirected_to_dashboard(): void
    {
        $kader = User::factory()->create([
            'role' => 'kader',
            'posyandu_id' => Posyandu::factory(),
            'email' => 'kader@example.test',
        ]);

        $response = $this->post(route('kader.login.store'), [
            'login' => 'kader@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('kader.dashboard'));
        $this->assertAuthenticatedAs($kader);
    }

    public function test_kader_can_login_with_phone_number(): void
    {
        $kader = User::factory()->create([
            'role' => 'kader',
            'posyandu_id' => Posyandu::factory(),
            'phone' => '081200000001',
        ]);

        $this->post(route('kader.login.store'), [
            'login' => '081200000001',
            'password' => 'password',
        ])->assertRedirect(route('kader.dashboard'));

        $this->assertAuthenticatedAs($kader);
    }

    public function test_parent_cannot_use_the_kader_login_portal(): void
    {
        User::factory()->create([
            'role' => 'parent',
            'email' => 'orangtua@example.test',
        ]);

        $this->post(route('kader.login.store'), [
            'login' => 'orangtua@example.test',
            'password' => 'password',
        ])->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_inactive_kader_cannot_login(): void
    {
        User::factory()->inactive()->create([
            'role' => 'kader',
            'posyandu_id' => Posyandu::factory(),
            'email' => 'nonaktif@example.test',
        ]);

        $this->post(route('kader.login.store'), [
            'login' => 'nonaktif@example.test',
            'password' => 'password',
        ])->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_parent_can_login_and_is_redirected_to_parent_dashboard(): void
    {
        $parent = User::factory()->create([
            'role' => 'parent',
            'email' => 'rina@example.test',
        ]);

        $this->post(route('orang-tua.login.store'), [
            'login' => 'rina@example.test',
            'password' => 'password',
        ])->assertRedirect(route('orang-tua.dashboard'));

        $this->assertAuthenticatedAs($parent);
    }

    public function test_kader_cannot_use_the_parent_login_portal(): void
    {
        User::factory()->create([
            'role' => 'kader',
            'posyandu_id' => Posyandu::factory(),
            'email' => 'kader@example.test',
        ]);

        $this->post(route('orang-tua.login.store'), [
            'login' => 'kader@example.test',
            'password' => 'password',
        ])->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_authenticated_users_are_redirected_away_from_login_pages(): void
    {
        $kader = User::factory()->create([
            'role' => 'kader',
            'posyandu_id' => Posyandu::factory(),
        ]);

        $this->actingAs($kader)
            ->get(route('kader.login'))
            ->assertRedirect(route('kader.dashboard'));

        $this->actingAs($kader)
            ->get(route('orang-tua.login'))
            ->assertRedirect(route('kader.dashboard'));
    }

    public function test_role_middleware_blocks_cross_portal_access(): void
    {
        $kader = User::factory()->create([
            'role' => 'kader',
            'posyandu_id' => Posyandu::factory(),
        ]);
        $parent = User::factory()->create(['role' => 'parent']);

        $this->actingAs($kader)->get(route('orang-tua.dashboard'))->assertForbidden();
        $this->actingAs($parent)->get(route('kader.dashboard'))->assertForbidden();
    }

    public function test_inactive_user_is_logged_out_by_role_middleware(): void
    {
        $parent = User::factory()->inactive()->create(['role' => 'parent']);

        $this->actingAs($parent)
            ->get(route('orang-tua.dashboard'))
            ->assertRedirect(route('orang-tua.login'))
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_logout_redirects_to_the_matching_login_page(): void
    {
        $kader = User::factory()->create([
            'role' => 'kader',
            'posyandu_id' => Posyandu::factory(),
        ]);

        $this->actingAs($kader)
            ->post(route('logout'))
            ->assertRedirect(route('kader.login'));

        $this->assertGuest();

        $parent = User::factory()->create(['role' => 'parent']);

        $this->actingAs($parent)
            ->post(route('logout'))
            ->assertRedirect(route('orang-tua.login'));
    }
}
