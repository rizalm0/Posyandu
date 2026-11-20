<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\Immunization;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImmunizationTest extends TestCase
{
    use RefreshDatabase;

    private Posyandu $posyandu;

    private User $kader;

    private Balita $balita;

    protected function setUp(): void
    {
        parent::setUp();

        $this->posyandu = Posyandu::factory()->create();
        $this->kader = User::factory()->create([
            'role' => 'kader',
            'posyandu_id' => $this->posyandu->id,
        ]);
        $this->balita = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
        ]);
    }

    public function test_kader_can_record_an_immunization_with_a_status(): void
    {
        $this->actingAs($this->kader)
            ->post(route('kader.imunisasi.store', $this->balita), [
                'name' => 'DPT-HB-Hib 1',
                'administered_at' => today()->subMonth()->toDateString(),
                'status' => 'needs_review',
                'notes' => 'Tanggal perlu dikonfirmasi dengan buku KIA.',
            ])
            ->assertRedirect(route('kader.balita.show', $this->balita));

        $immunization = Immunization::firstOrFail();

        $this->assertSame('needs_review', $immunization->status);
        $this->assertSame($this->balita->id, $immunization->balita_id);
        $this->assertSame($this->kader->id, $immunization->recorded_by);
    }

    public function test_immunization_requires_name_past_date_and_valid_status(): void
    {
        $this->actingAs($this->kader)
            ->post(route('kader.imunisasi.store', $this->balita), [
                'name' => '',
                'administered_at' => today()->addDay()->toDateString(),
                'status' => 'given',
            ])
            ->assertSessionHasErrors(['name', 'administered_at', 'status']);

        $this->assertDatabaseCount('immunizations', 0);
    }

    public function test_immunization_notes_are_optional(): void
    {
        $this->actingAs($this->kader)
            ->post(route('kader.imunisasi.store', $this->balita), [
                'name' => 'BCG',
                'administered_at' => today()->toDateString(),
                'status' => 'recorded',
                'notes' => '',
            ])
            ->assertRedirect(route('kader.balita.show', $this->balita));

        $this->assertDatabaseHas('immunizations', [
            'balita_id' => $this->balita->id,
            'name' => 'BCG',
            'notes' => null,
            'status' => 'recorded',
        ]);
    }

    public function test_kader_can_update_an_immunization_record(): void
    {
        $immunization = $this->balita->immunizations()->create([
            'name' => 'Polio 1',
            'administered_at' => today()->subMonths(2)->toDateString(),
            'status' => 'recorded',
            'recorded_by' => $this->kader->id,
        ]);

        $this->actingAs($this->kader)
            ->put(route('kader.imunisasi.update', $immunization), [
                'name' => 'Polio 1',
                'administered_at' => $immunization->administered_at->toDateString(),
                'status' => 'needs_review',
                'notes' => 'Menunggu konfirmasi buku KIA.',
            ])
            ->assertRedirect(route('kader.balita.show', $this->balita));

        $immunization->refresh();

        $this->assertSame('needs_review', $immunization->status);
        $this->assertSame('Menunggu konfirmasi buku KIA.', $immunization->notes);
    }

    public function test_kader_cannot_update_immunization_from_another_posyandu(): void
    {
        $otherPosyandu = Posyandu::factory()->create();
        $otherBalita = Balita::factory()->create(['posyandu_id' => $otherPosyandu->id]);
        $otherImmunization = $otherBalita->immunizations()->create([
            'name' => 'Campak/MR',
            'administered_at' => today()->toDateString(),
            'status' => 'recorded',
        ]);

        $this->actingAs($this->kader)
            ->get(route('kader.imunisasi.edit', $otherImmunization))
            ->assertForbidden();

        $this->actingAs($this->kader)
            ->put(route('kader.imunisasi.update', $otherImmunization), [
                'name' => 'Diubah',
                'administered_at' => today()->toDateString(),
                'status' => 'recorded',
            ])
            ->assertForbidden();
    }

    public function test_creating_an_examination_does_not_auto_mark_immunizations(): void
    {
        $this->actingAs($this->kader)
            ->post(route('kader.pemeriksaan.store', $this->balita), [
                'examined_at' => today()->toDateString(),
                'weight_kg' => 9.8,
                'height_cm' => 80.5,
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('examinations', 1);
        $this->assertDatabaseCount('immunizations', 0);
    }

    public function test_verified_parent_can_see_only_their_childs_immunization_history(): void
    {
        $parent = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $this->posyandu->id,
        ]);
        $otherParent = User::factory()->create([
            'role' => 'parent',
            'posyandu_id' => $this->posyandu->id,
        ]);
        $otherBalita = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
            'full_name' => 'Anak Lain',
        ]);

        $this->balita->guardians()->attach($parent->id, [
            'relationship' => 'mother',
            'verified_at' => now(),
        ]);
        $otherBalita->guardians()->attach($otherParent->id, [
            'relationship' => 'mother',
            'verified_at' => now(),
        ]);

        $this->balita->immunizations()->create([
            'name' => 'Imunisasi Anak Sendiri',
            'administered_at' => today()->toDateString(),
            'status' => 'recorded',
        ]);
        $otherBalita->immunizations()->create([
            'name' => 'Imunisasi Anak Lain',
            'administered_at' => today()->toDateString(),
            'status' => 'recorded',
        ]);

        $this->actingAs($parent)
            ->get(route('orang-tua.imunisasi', $this->balita))
            ->assertOk()
            ->assertSee('Imunisasi Anak Sendiri')
            ->assertDontSee('Imunisasi Anak Lain');

        $this->actingAs($parent)
            ->get(route('orang-tua.imunisasi', $otherBalita))
            ->assertForbidden();
    }
}
