<?php

namespace Tests\Feature\Api;

use App\Models\Balita;
use App\Models\Posyandu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalitaApiTest extends TestCase
{
    use RefreshDatabase;

    private function posyandu(): Posyandu
    {
        return Posyandu::factory()->create();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Aisyah Putri',
            'date_of_birth' => '2024-05-12',
            'gender' => 'P',
            'parent_name' => 'Rina Kartika',
            'phone' => '081200000001',
            'address' => 'Jl. Melati No. 8',
        ], $overrides);
    }

    public function test_index_returns_all_balitas(): void
    {
        $posyandu = $this->posyandu();
        Balita::factory()->count(2)->create(['posyandu_id' => $posyandu->id]);

        $this->getJson('/api/balita')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'registration_number', 'full_name', 'date_of_birth', 'gender', 'parent_name', 'is_active'],
                ],
            ]);
    }

    public function test_store_creates_balita_and_generates_registration_number(): void
    {
        $posyandu = $this->posyandu();

        $response = $this->postJson('/api/balita', $this->payload());

        $response->assertCreated()
            ->assertJsonPath('data.full_name', 'Aisyah Putri')
            ->assertJsonPath('data.gender', 'P');

        $this->assertMatchesRegularExpression('/^PSD-\d{4}-\d{4}$/', $response->json('data.registration_number'));
        $this->assertDatabaseHas('balitas', [
            'full_name' => 'Aisyah Putri',
            'posyandu_id' => $posyandu->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->posyandu();

        $this->postJson('/api/balita', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['full_name', 'date_of_birth', 'gender', 'parent_name']);
    }

    public function test_show_returns_a_balita(): void
    {
        $balita = Balita::factory()->create();

        $this->getJson("/api/balita/{$balita->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $balita->id)
            ->assertJsonPath('data.full_name', $balita->full_name);
    }

    public function test_update_changes_balita_data(): void
    {
        $balita = Balita::factory()->create(['full_name' => 'Nama Lama']);

        $this->putJson("/api/balita/{$balita->id}", [
            'full_name' => 'Nama Baru',
            'gender' => 'L',
            'address' => 'Alamat baru',
        ])
            ->assertOk()
            ->assertJsonPath('data.full_name', 'Nama Baru')
            ->assertJsonPath('data.gender', 'L');

        $this->assertDatabaseHas('balitas', [
            'id' => $balita->id,
            'full_name' => 'Nama Baru',
            'address' => 'Alamat baru',
        ]);
    }

    public function test_destroy_deletes_balita_from_database(): void
    {
        $balita = Balita::factory()->create();

        $this->deleteJson("/api/balita/{$balita->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Data balita berhasil dihapus.');

        $this->assertDatabaseMissing('balitas', ['id' => $balita->id]);
    }

    public function test_show_returns_404_for_unknown_id(): void
    {
        $this->getJson('/api/balita/99999')->assertNotFound();
    }
}
