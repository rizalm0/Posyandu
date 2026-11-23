<?php

namespace Tests\Feature\Api;

use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KaderApiTest extends TestCase
{
    use RefreshDatabase;

    private function posyandu(): Posyandu
    {
        return Posyandu::factory()->create();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Dewi Lestari',
            'email' => 'dewi.api@example.test',
            'phone' => '081200000003',
            'password' => 'secret123',
        ], $overrides);
    }

    public function test_index_returns_only_kader_accounts(): void
    {
        $posyandu = $this->posyandu();
        User::factory()->create(['role' => 'kader', 'posyandu_id' => $posyandu->id]);
        User::factory()->create(['role' => 'parent', 'posyandu_id' => $posyandu->id]);

        $this->getJson('/api/kader')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.role', 'kader');
    }

    public function test_store_creates_kader_with_hashed_password(): void
    {
        $posyandu = $this->posyandu();

        $response = $this->postJson('/api/kader', $this->payload());

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Dewi Lestari')
            ->assertJsonPath('data.role', 'kader');

        $kader = User::where('email', 'dewi.api@example.test')->firstOrFail();

        $this->assertSame($posyandu->id, $kader->posyandu_id);
        $this->assertTrue(Hash::check('secret123', $kader->password));
    }

    public function test_store_validates_required_fields(): void
    {
        $this->posyandu();

        $this->postJson('/api/kader', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_update_changes_kader_data(): void
    {
        $kader = User::factory()->create([
            'role' => 'kader',
            'email' => 'dewi.api@example.test',
            'name' => 'Nama Lama',
        ]);

        $this->putJson("/api/kader/{$kader->id}", [
            'name' => 'Nama Baru',
            'phone' => '081277777777',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nama Baru');

        $this->assertDatabaseHas('users', [
            'id' => $kader->id,
            'name' => 'Nama Baru',
            'phone' => '081277777777',
        ]);
    }

    public function test_destroy_deletes_kader_from_database(): void
    {
        $kader = User::factory()->create(['role' => 'kader']);

        $this->deleteJson("/api/kader/{$kader->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Akun kader berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $kader->id]);
    }

    public function test_kader_endpoint_rejects_parent_id(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);

        $this->getJson("/api/kader/{$parent->id}")->assertNotFound();
    }
}
