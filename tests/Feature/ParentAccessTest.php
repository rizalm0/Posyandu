<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentAccessTest extends TestCase
{
    use RefreshDatabase;

    private Posyandu $posyandu;

    private User $parent;

    private User $otherParent;

    private Balita $child;

    private Balita $otherChild;

    protected function setUp(): void
    {
        parent::setUp();

        $this->posyandu = Posyandu::factory()->create();
        $this->parent = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $this->posyandu->id,
            'email' => 'rina@example.test',
        ]);
        $this->otherParent = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $this->posyandu->id,
            'email' => 'lain@example.test',
        ]);
        $this->child = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
            'full_name' => 'Aisyah Putri',
            'date_of_birth' => '2023-11-18',
            'gender' => 'P',
        ]);
        $this->otherChild = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
            'full_name' => 'Bima Aditya',
        ]);

        $this->child->guardians()->attach($this->parent->id, [
            'relationship' => 'mother',
            'is_primary' => true,
            'verified_at' => now(),
        ]);
    }

    public function test_verified_parent_sees_child_dashboard_and_latest_measurement(): void
    {
        $this->child->examinations()->create([
            'posyandu_id' => $this->posyandu->id,
            'examined_at' => today()->subDays(3)->toDateString(),
            'weight_kg' => 12.5,
            'height_cm' => 91.2,
            'head_circumference_cm' => 48.4,
            'notes' => 'Aktif dan tidak ada keluhan.',
        ]);

        $this->actingAs($this->parent)
            ->get(route('orang-tua.dashboard'))
            ->assertOk()
            ->assertSee('Aisyah Putri')
            ->assertSee('12.5')
            ->assertSee('91.2')
            ->assertSee('Aktif dan tidak ada keluhan.')
            ->assertDontSee('Bima Aditya');
    }

    public function test_verified_parent_can_view_growth_history_and_immunization_pages(): void
    {
        $this->child->examinations()->create([
            'posyandu_id' => $this->posyandu->id,
            'examined_at' => today()->subMonths(2)->toDateString(),
            'weight_kg' => 11.9,
            'height_cm' => 88.6,
        ]);
        $this->child->examinations()->create([
            'posyandu_id' => $this->posyandu->id,
            'examined_at' => today()->subMonth()->toDateString(),
            'weight_kg' => 12.2,
            'height_cm' => 90.0,
        ]);
        $this->child->immunizations()->create([
            'name' => 'Campak/MR',
            'administered_at' => today()->subMonths(3)->toDateString(),
            'status' => 'recorded',
        ]);

        $this->actingAs($this->parent)
            ->get(route('orang-tua.grafik', $this->child))
            ->assertOk()
            ->assertSee('Grafik Pertumbuhan')
            ->assertSee('Aisyah Putri')
            ->assertSee('data-chart', false)
            ->assertSee('11.9');

        $this->actingAs($this->parent)
            ->get(route('orang-tua.riwayat', $this->child))
            ->assertOk()
            ->assertSee('Riwayat Pemeriksaan')
            ->assertSee('90.0');

        $this->actingAs($this->parent)
            ->get(route('orang-tua.imunisasi', $this->child))
            ->assertOk()
            ->assertSee('Riwayat Imunisasi')
            ->assertSee('Campak/MR');
    }

    public function test_unverified_guardian_link_does_not_grant_child_access(): void
    {
        $unverified = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $this->posyandu->id,
        ]);
        $this->child->guardians()->attach($unverified->id, [
            'relationship' => 'father',
            'verified_at' => null,
        ]);

        $this->actingAs($unverified)
            ->get(route('orang-tua.grafik', $this->child))
            ->assertForbidden();

        $this->actingAs($unverified)
            ->get(route('orang-tua.riwayat', $this->child))
            ->assertForbidden();

        $this->actingAs($unverified)
            ->get(route('orang-tua.imunisasi', $this->child))
            ->assertForbidden();

        $this->actingAs($unverified)
            ->get(route('orang-tua.dashboard'))
            ->assertOk()
            ->assertSee('belum terhubung ke data balita');
    }

    public function test_verified_guardian_gains_access_after_kader_verification(): void
    {
        $pending = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $this->posyandu->id,
        ]);
        $this->child->guardians()->attach($pending->id, [
            'relationship' => 'guardian',
            'verified_at' => null,
        ]);

        $this->actingAs($pending)
            ->get(route('orang-tua.grafik', $this->child))
            ->assertForbidden();

        $this->child->guardians()->updateExistingPivot($pending->id, [
            'verified_at' => now(),
        ]);

        $this->actingAs($pending)
            ->get(route('orang-tua.grafik', $this->child))
            ->assertOk()
            ->assertSee('Aisyah Putri');
    }

    public function test_parent_cannot_access_a_child_with_matching_name_and_birth_date_without_an_explicit_link(): void
    {
        $lookalike = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
            'full_name' => 'Aisyah Putri',
            'date_of_birth' => '2023-11-18',
            'gender' => 'P',
        ]);

        $this->assertSame($lookalike->full_name, $this->child->full_name);
        $this->assertSame(
            $lookalike->date_of_birth->toDateString(),
            $this->child->date_of_birth->toDateString()
        );

        $this->actingAs($this->parent)
            ->get(route('orang-tua.grafik', $lookalike))
            ->assertForbidden();

        $this->actingAs($this->otherParent)
            ->get(route('orang-tua.grafik', $this->child))
            ->assertForbidden();
    }

    public function test_requested_child_parameter_cannot_select_an_unlinked_child(): void
    {
        $this->actingAs($this->parent)
            ->get(route('orang-tua.dashboard', ['anak' => $this->otherChild->id]))
            ->assertOk()
            ->assertSee('Aisyah Putri')
            ->assertDontSee('Bima Aditya');
    }

    public function test_parent_with_several_children_can_switch_between_them(): void
    {
        $second = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
            'full_name' => 'Bima Aditya',
        ]);
        $second->guardians()->attach($this->parent->id, [
            'relationship' => 'mother',
            'verified_at' => now(),
        ]);

        $this->actingAs($this->parent)
            ->get(route('orang-tua.dashboard'))
            ->assertOk()
            ->assertSee('Aisyah Putri')
            ->assertSee('Bima Aditya');

        $this->actingAs($this->parent)
            ->get(route('orang-tua.dashboard', ['anak' => $second->id]))
            ->assertOk()
            ->assertSee('Bima Aditya');
    }

    public function test_both_parents_with_verified_links_can_view_the_same_child(): void
    {
        $this->child->guardians()->attach($this->otherParent->id, [
            'relationship' => 'father',
            'verified_at' => now(),
        ]);

        foreach ([$this->parent, $this->otherParent] as $parent) {
            $this->actingAs($parent)
                ->get(route('orang-tua.grafik', $this->child))
                ->assertOk()
                ->assertSee('Aisyah Putri');
        }
    }

    public function test_parent_without_children_sees_the_contact_kader_state(): void
    {
        $this->actingAs($this->otherParent)
            ->get(route('orang-tua.dashboard'))
            ->assertOk()
            ->assertSee('Akun Anda belum terhubung ke data balita')
            ->assertSee('Hubungkan akun hanya dapat dilakukan oleh kader Posyandu.');
    }
}
