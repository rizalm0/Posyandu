<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\Examination;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExaminationTest extends TestCase
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

    public function test_kader_can_open_the_examination_form(): void
    {
        $this->actingAs($this->kader)
            ->get(route('kader.pemeriksaan.create', $this->balita))
            ->assertOk()
            ->assertSee('Catat Pemeriksaan')
            ->assertSee($this->balita->full_name);
    }

    public function test_kader_can_save_an_examination(): void
    {
        $response = $this->actingAs($this->kader)->post(route('kader.pemeriksaan.store', $this->balita), [
            'examined_at' => today()->toDateString(),
            'weight_kg' => 9.8,
            'height_cm' => 80.5,
            'head_circumference_cm' => 46.2,
            'notes' => 'Anak aktif dan tidak ada keluhan.',
        ]);

        $examination = Examination::firstOrFail();

        $response->assertRedirect(route('kader.pemeriksaan.success', $examination));

        $this->assertDatabaseHas('examinations', [
            'id' => $examination->id,
            'posyandu_id' => $this->posyandu->id,
            'balita_id' => $this->balita->id,
            'recorded_by' => $this->kader->id,
        ]);

        $this->actingAs($this->kader)
            ->get(route('kader.pemeriksaan.success', $examination))
            ->assertOk()
            ->assertSee('Pemeriksaan berhasil disimpan')
            ->assertSee($this->balita->full_name);
    }

    public function test_examination_form_shows_validation_errors_for_invalid_input(): void
    {
        $this->actingAs($this->kader)
            ->post(route('kader.pemeriksaan.store', $this->balita), [
                'examined_at' => today()->addDay()->toDateString(),
                'weight_kg' => 0,
                'height_cm' => 300,
                'head_circumference_cm' => 500,
                'notes' => str_repeat('a', 1001),
            ])
            ->assertSessionHasErrors([
                'examined_at',
                'weight_kg',
                'height_cm',
                'head_circumference_cm',
                'notes',
            ]);

        $this->assertDatabaseCount('examinations', 0);
    }

    public function test_examination_requires_weight_and_height(): void
    {
        $this->actingAs($this->kader)
            ->post(route('kader.pemeriksaan.store', $this->balita), [
                'examined_at' => today()->toDateString(),
            ])
            ->assertSessionHasErrors(['weight_kg', 'height_cm']);

        $this->assertDatabaseCount('examinations', 0);
    }

    public function test_form_displays_the_save_failure_state_and_preserves_input(): void
    {
        $this->actingAs($this->kader)
            ->withSession([
                'error' => 'Pemeriksaan belum tersimpan. Periksa koneksi lalu coba lagi.',
                '_old_input' => ['weight_kg' => '9.8'],
            ])
            ->get(route('kader.pemeriksaan.create', $this->balita))
            ->assertOk()
            ->assertSee('Pemeriksaan belum tersimpan')
            ->assertSee('Data yang sudah Anda isi tetap ada di formulir ini.')
            ->assertSee('9.8')
            ->assertDontSee('Tutup pemberitahuan');
    }

    public function test_store_failure_returns_to_the_form_with_an_error(): void
    {
        Schema::drop('examinations');

        $this->actingAs($this->kader)
            ->from(route('kader.pemeriksaan.create', $this->balita))
            ->post(route('kader.pemeriksaan.store', $this->balita), [
                'examined_at' => today()->toDateString(),
                'weight_kg' => 9.8,
                'height_cm' => 80.5,
            ])
            ->assertRedirect(route('kader.pemeriksaan.create', $this->balita))
            ->assertSessionHas('error');

        $this->assertNull(session('success'));
    }

    public function test_kader_can_update_an_examination(): void
    {
        $examination = $this->balita->examinations()->create([
            'posyandu_id' => $this->posyandu->id,
            'examined_at' => today()->subWeek()->toDateString(),
            'weight_kg' => 9.5,
            'height_cm' => 80.0,
            'recorded_by' => $this->kader->id,
        ]);

        $this->actingAs($this->kader)
            ->put(route('kader.pemeriksaan.update', $examination), [
                'examined_at' => $examination->examined_at->toDateString(),
                'weight_kg' => 9.9,
                'height_cm' => 81.0,
                'head_circumference_cm' => 46.5,
                'notes' => 'Diperbarui setelah penimbangan ulang.',
            ])
            ->assertRedirect(route('kader.balita.show', $this->balita));

        $examination->refresh();

        $this->assertSame('9.90', $examination->weight_kg);
        $this->assertSame('81.0', $examination->height_cm);
    }

    public function test_kader_cannot_create_or_update_examinations_for_another_posyandu(): void
    {
        $otherPosyandu = Posyandu::factory()->create();
        $otherKader = User::factory()->create([
            'role' => 'kader',
            'posyandu_id' => $otherPosyandu->id,
        ]);
        $otherBalita = Balita::factory()->create([
            'posyandu_id' => $otherPosyandu->id,
        ]);
        $otherExamination = $otherBalita->examinations()->create([
            'posyandu_id' => $otherPosyandu->id,
            'examined_at' => today()->toDateString(),
            'weight_kg' => 10,
            'height_cm' => 80,
        ]);

        $this->actingAs($this->kader)
            ->post(route('kader.pemeriksaan.store', $otherBalita), [
                'examined_at' => today()->toDateString(),
                'weight_kg' => 9.8,
                'height_cm' => 80.5,
            ])
            ->assertForbidden();

        $this->actingAs($this->kader)
            ->put(route('kader.pemeriksaan.update', $otherExamination), [
                'examined_at' => today()->toDateString(),
                'weight_kg' => 11,
                'height_cm' => 82,
            ])
            ->assertForbidden();

        $this->assertSame('10.00', $otherExamination->fresh()->weight_kg);
    }

    public function test_parent_cannot_access_examination_management_routes(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);
        $examination = $this->balita->examinations()->create([
            'posyandu_id' => $this->posyandu->id,
            'examined_at' => today()->toDateString(),
            'weight_kg' => 10,
            'height_cm' => 80,
        ]);

        $this->actingAs($parent)
            ->get(route('kader.pemeriksaan.create', $this->balita))
            ->assertForbidden();

        $this->actingAs($parent)
            ->get(route('kader.pemeriksaan.success', $examination))
            ->assertForbidden();
    }
}
