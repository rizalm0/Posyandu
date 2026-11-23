<?php

namespace Tests\Feature\Api;

use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ParentApiTest extends TestCase
{
    use RefreshDatabase;

    private function posyandu(): Posyandu
    {
        return Posyandu::factory()->create();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Rina Kartika',
            'email' => 'rina.api@example.test',
            'phone' => '081200000002',
            'password' => 'secret123',
        ], $overrides);
    }

    public function test_index_returns_only_parent_accounts(): void
    {
        $posyandu = $this->posyandu();
        User::factory()->create(['role' => 'parent', 'posyandu_id' => $posyandu->id]);
        User::factory()->create(['role' => 'kader', 'posyandu_id' => $posyandu->id]);

        $this->getJson('/api/orang-tua')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.role', 'parent')
            ->assertJsonPath('data.0.children_count', 0);
    }

    public function test_store_creates_parent_with_hashed_password(): void
    {
        $posyandu = $this->posyandu();

        $response = $this->postJson('/api/orang-tua', $this->payload());

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Rina Kartika')
            ->assertJsonPath('data.role', 'parent')
            ->assertJsonPath('data.email', 'rina.api@example.test');

        $parent = User::where('email', 'rina.api@example.test')->firstOrFail();

        $this->assertSame($posyandu->id, $parent->posyandu_id);
        $this->assertTrue($parent->is_active);
        $this->assertTrue(Hash::check('secret123', $parent->password));
    }

    public function test_store_rejects_duplicate_email(): void
    {
        $this->posyandu();
        User::factory()->create(['email' => 'rina.api@example.test']);

        $this->postJson('/api/orang-tua', $this->payload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->posyandu();

        $this->postJson('/api/orang-tua', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_update_changes_parent_data(): void
    {
        $parent = User::factory()->create([
            'role' => 'parent',
            'email' => 'rina.api@example.test',
            'name' => 'Nama Lama',
        ]);

        $this->putJson("/api/orang-tua/{$parent->id}", [
            'name' => 'Nama Baru',
            'email' => 'rina.api@example.test',
            'phone' => '081299999999',
            'is_active' => false,
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nama Baru')
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('users', [
            'id' => $parent->id,
            'name' => 'Nama Baru',
            'phone' => '081299999999',
        ]);
    }

    public function test_update_can_change_password(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);

        $this->putJson("/api/orang-tua/{$parent->id}", [
            'password' => 'password-baru',
        ])->assertOk();

        $this->assertTrue(Hash::check('password-baru', $parent->refresh()->password));
    }

    public function test_destroy_deletes_parent_from_database(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);

        $this->deleteJson("/api/orang-tua/{$parent->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Akun orang tua berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $parent->id]);
    }

    public function test_parent_endpoint_rejects_kader_id(): void
    {
        $kader = User::factory()->create(['role' => 'kader']);

        $this->getJson("/api/orang-tua/{$kader->id}")->assertNotFound();
    }
}
