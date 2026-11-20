<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalitaManagementTest extends TestCase
{
    use RefreshDatabase;

    private function kader(Posyandu $posyandu): User
    {
        return User::factory()->create([
            'role' => 'kader',
            'posyandu_id' => $posyandu->id,
        ]);
    }

    public function test_kader_can_register_a_new_balita_in_their_posyandu(): void
    {
        $posyandu = Posyandu::factory()->create();
        $kader = $this->kader($posyandu);

        $response = $this->actingAs($kader)->post(route('kader.balita.store'), [
            'full_name' => 'Aisyah Putri',
            'date_of_birth' => '2024-05-12',
            'gender' => 'P',
            'parent_name' => 'Rina Kartika',
            'phone' => '081200000001',
            'address' => 'Jl. Melati No. 8',
            'registered_at' => today()->toDateString(),
        ]);

        $balita = Balita::where('full_name', 'Aisyah Putri')->firstOrFail();

        $response->assertRedirect(route('kader.balita.show', $balita));
        $this->assertSame($posyandu->id, $balita->posyandu_id);
        $this->assertTrue($balita->is_active);
        $this->assertMatchesRegularExpression('/^PSD-\d{4}-\d{4}$/', $balita->registration_number);
    }

    public function test_registration_number_increments_within_the_posyandu(): void
    {
        $posyandu = Posyandu::factory()->create();
        $kader = $this->kader($posyandu);

        Balita::factory()->create([
            'posyandu_id' => $posyandu->id,
            'registration_number' => 'PSD-'.now()->format('Y').'-0001',
        ]);

        $this->actingAs($kader)->post(route('kader.balita.store'), [
            'full_name' => 'Bima Aditya',
            'date_of_birth' => '2025-01-10',
            'gender' => 'L',
            'parent_name' => 'Budi Santoso',
            'registered_at' => today()->toDateString(),
        ]);

        $created = Balita::where('full_name', 'Bima Aditya')->firstOrFail();

        $this->assertSame('PSD-'.now()->format('Y').'-0002', $created->registration_number);
    }

    public function test_balita_registration_requires_valid_fields(): void
    {
        $posyandu = Posyandu::factory()->create();
        $kader = $this->kader($posyandu);

        $this->actingAs($kader)
            ->post(route('kader.balita.store'), [])
            ->assertSessionHasErrors([
                'full_name',
                'date_of_birth',
                'gender',
                'parent_name',
                'registered_at',
            ]);

        $this->actingAs($kader)
            ->post(route('kader.balita.store'), [
                'full_name' => 'Test',
                'date_of_birth' => today()->addDay()->toDateString(),
                'gender' => 'X',
                'parent_name' => 'Test',
                'registered_at' => today()->addDay()->toDateString(),
            ])
            ->assertSessionHasErrors(['date_of_birth', 'gender', 'registered_at']);

        $this->assertDatabaseCount('balitas', 0);
    }

    public function test_kader_can_update_a_balita_in_their_posyandu(): void
    {
        $posyandu = Posyandu::factory()->create();
        $kader = $this->kader($posyandu);
        $balita = Balita::factory()->create(['posyandu_id' => $posyandu->id]);

        $this->actingAs($kader)
            ->put(route('kader.balita.update', $balita), [
                'full_name' => 'Nama Diperbarui',
                'date_of_birth' => $balita->date_of_birth->toDateString(),
                'gender' => $balita->gender,
                'parent_name' => 'Orang Tua Baru',
                'phone' => '081299999999',
                'address' => 'Alamat baru',
                'registered_at' => $balita->registered_at->toDateString(),
            ])
            ->assertRedirect(route('kader.balita.show', $balita));

        $this->assertDatabaseHas('balitas', [
            'id' => $balita->id,
            'full_name' => 'Nama Diperbarui',
            'parent_name' => 'Orang Tua Baru',
        ]);
    }

    public function test_kader_cannot_view_or_change_balita_from_another_posyandu(): void
    {
        $posyanduA = Posyandu::factory()->create();
        $posyanduB = Posyandu::factory()->create();
        $kaderA = $this->kader($posyanduA);
        $balitaB = Balita::factory()->create(['posyandu_id' => $posyanduB->id]);

        $this->actingAs($kaderA)->get(route('kader.balita.show', $balitaB))->assertForbidden();
        $this->actingAs($kaderA)->get(route('kader.balita.edit', $balitaB))->assertForbidden();
        $this->actingAs($kaderA)->get(route('kader.pemeriksaan.create', $balitaB))->assertForbidden();

        $this->actingAs($kaderA)
            ->put(route('kader.balita.update', $balitaB), [
                'full_name' => 'Diubah',
                'date_of_birth' => $balitaB->date_of_birth->toDateString(),
                'gender' => $balitaB->gender,
                'parent_name' => 'X',
                'registered_at' => $balitaB->registered_at->toDateString(),
            ])
            ->assertForbidden();

        $this->assertSame($balitaB->full_name, $balitaB->fresh()->full_name);
    }

    public function test_balita_list_only_contains_the_kaders_posyandu(): void
    {
        $posyanduA = Posyandu::factory()->create();
        $posyanduB = Posyandu::factory()->create();
        $kaderA = $this->kader($posyanduA);

        Balita::factory()->create([
            'posyandu_id' => $posyanduA->id,
            'full_name' => 'Anak Posyandu A',
        ]);
        Balita::factory()->create([
            'posyandu_id' => $posyanduB->id,
            'full_name' => 'Anak Posyandu B',
        ]);

        $this->actingAs($kaderA)
            ->get(route('kader.balita.index'))
            ->assertOk()
            ->assertSee('Anak Posyandu A')
            ->assertDontSee('Anak Posyandu B');
    }

    public function test_balita_list_supports_search_and_administrative_filters(): void
    {
        $posyandu = Posyandu::factory()->create();
        $kader = $this->kader($posyandu);

        $belumDiperiksa = Balita::factory()->create([
            'posyandu_id' => $posyandu->id,
            'full_name' => 'Anak Belum Diperiksa',
            'parent_name' => 'Ibu Pencarian',
        ]);
        $sudahDiperiksa = Balita::factory()->create([
            'posyandu_id' => $posyandu->id,
            'full_name' => 'Anak Sudah Diperiksa',
        ]);

        $sudahDiperiksa->examinations()->create([
            'posyandu_id' => $posyandu->id,
            'examined_at' => today()->subDays(5)->toDateString(),
            'weight_kg' => 12.5,
            'height_cm' => 90.0,
        ]);

        $this->actingAs($kader)
            ->get(route('kader.balita.index', ['status' => 'belum_diperiksa']))
            ->assertOk()
            ->assertSee($belumDiperiksa->full_name)
            ->assertDontSee($sudahDiperiksa->full_name);

        $this->actingAs($kader)
            ->get(route('kader.balita.index', ['q' => 'Ibu Pencarian']))
            ->assertOk()
            ->assertSee($belumDiperiksa->full_name)
            ->assertDontSee($sudahDiperiksa->full_name);
    }

    public function test_kader_can_create_parent_account_without_granting_child_access(): void
    {
        $posyandu = Posyandu::factory()->create();
        $kader = $this->kader($posyandu);

        $this->actingAs($kader)
            ->post(route('kader.orang-tua.store'), [
                'name' => 'Rina Kartika',
                'email' => 'rina@example.test',
                'phone' => '081298765432',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('kader.orang-tua.index'));

        $parent = User::where('email', 'rina@example.test')->firstOrFail();

        $this->assertSame('parent', $parent->role);
        $this->assertTrue($parent->is_active);
        $this->assertSame($posyandu->id, $parent->posyandu_id);
        $this->assertCount(0, $parent->children);
    }

    public function test_parent_account_email_must_be_unique_and_password_confirmed(): void
    {
        $posyandu = Posyandu::factory()->create();
        $kader = $this->kader($posyandu);

        User::factory()->create([
            'role' => 'parent',
            'email' => 'already@example.test',
            'posyandu_id' => $posyandu->id,
        ]);

        $this->actingAs($kader)
            ->post(route('kader.orang-tua.store'), [
                'name' => 'Akun Baru',
                'email' => 'already@example.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertSessionHasErrors('email');

        $this->actingAs($kader)
            ->post(route('kader.orang-tua.store'), [
                'name' => 'Akun Baru',
                'email' => 'baru@example.test',
                'password' => 'password123',
                'password_confirmation' => 'berbeda',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_kader_cannot_edit_parent_account_from_another_posyandu(): void
    {
        $posyanduA = Posyandu::factory()->create();
        $posyanduB = Posyandu::factory()->create();
        $kaderA = $this->kader($posyanduA);
        $parentB = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $posyanduB->id,
        ]);

        $this->actingAs($kaderA)
            ->get(route('kader.orang-tua.edit', $parentB))
            ->assertForbidden();

        $this->actingAs($kaderA)
            ->put(route('kader.orang-tua.update', $parentB), [
                'name' => 'Diubah',
                'email' => $parentB->email,
            ])
            ->assertForbidden();

        $this->assertNotSame('Diubah', $parentB->fresh()->name);
    }

    public function test_kader_can_link_a_verified_parent_to_a_balita(): void
    {
        $posyandu = Posyandu::factory()->create();
        $kader = $this->kader($posyandu);
        $parent = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $posyandu->id,
        ]);
        $balita = Balita::factory()->create(['posyandu_id' => $posyandu->id]);

        $this->actingAs($kader)
            ->post(route('kader.wali.store', $balita), [
                'email' => $parent->email,
                'relationship' => 'mother',
                'verify_now' => '1',
                'is_primary' => '1',
            ])
            ->assertRedirect();

        $pivot = $balita->guardians()->whereKey($parent->id)->firstOrFail()->pivot;

        $this->assertSame('mother', $pivot->relationship);
        $this->assertTrue((bool) $pivot->is_primary);
        $this->assertNotNull($pivot->verified_at);
    }

    public function test_one_balita_can_have_multiple_verified_guardians(): void
    {
        $posyandu = Posyandu::factory()->create();
        $kader = $this->kader($posyandu);
        $balita = Balita::factory()->create(['posyandu_id' => $posyandu->id]);
        $mother = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $posyandu->id,
        ]);
        $father = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $posyandu->id,
        ]);

        foreach ([[$mother, 'mother'], [$father, 'father']] as [$parent, $relationship]) {
            $this->actingAs($kader)->post(route('kader.wali.store', $balita), [
                'email' => $parent->email,
                'relationship' => $relationship,
                'verify_now' => '1',
            ])->assertRedirect();
        }

        $this->assertCount(2, $balita->guardians);
        $this->assertSame(2, $balita->verifiedGuardians()->count());
    }

    public function test_kader_cannot_link_a_non_parent_account_or_parent_from_another_posyandu(): void
    {
        $posyandu = Posyandu::factory()->create();
        $otherPosyandu = Posyandu::factory()->create();
        $kader = $this->kader($posyandu);
        $balita = Balita::factory()->create(['posyandu_id' => $posyandu->id]);
        $otherParent = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $otherPosyandu->id,
        ]);

        $this->actingAs($kader)
            ->post(route('kader.wali.store', $balita), [
                'email' => $kader->email,
                'relationship' => 'guardian',
            ])
            ->assertSessionHasErrors('email');

        $this->actingAs($kader)
            ->post(route('kader.wali.store', $balita), [
                'email' => $otherParent->email,
                'relationship' => 'guardian',
            ])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('balita_user', 0);
    }

    public function test_kader_can_verify_and_remove_a_guardian_link(): void
    {
        $posyandu = Posyandu::factory()->create();
        $kader = $this->kader($posyandu);
        $parent = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $posyandu->id,
        ]);
        $balita = Balita::factory()->create(['posyandu_id' => $posyandu->id]);

        $balita->guardians()->attach($parent->id, [
            'relationship' => 'mother',
            'is_primary' => true,
            'verified_at' => null,
        ]);

        $this->actingAs($kader)
            ->post(route('kader.wali.verify', [$balita, $parent]))
            ->assertRedirect();

        $this->assertNotNull(
            $balita->guardians()->whereKey($parent->id)->first()->pivot->verified_at
        );

        $this->actingAs($kader)
            ->delete(route('kader.wali.destroy', [$balita, $parent]))
            ->assertRedirect();

        $this->assertDatabaseMissing('balita_user', [
            'balita_id' => $balita->id,
            'user_id' => $parent->id,
        ]);
    }

    public function test_parent_cannot_access_cross_portal_balita_management(): void
    {
        $posyandu = Posyandu::factory()->create();
        $parent = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $posyandu->id,
        ]);

        $this->actingAs($parent)->get(route('kader.balita.index'))->assertForbidden();
        $this->actingAs($parent)->get(route('kader.orang-tua.index'))->assertForbidden();
        $this->actingAs($parent)->get(route('kader.laporan.index'))->assertForbidden();
    }
}
