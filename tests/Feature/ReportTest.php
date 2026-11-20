<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private Posyandu $posyandu;

    private User $kader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->posyandu = Posyandu::factory()->create();
        $this->kader = User::factory()->create([
            'role' => 'kader',
            'posyandu_id' => $this->posyandu->id,
        ]);
    }

    public function test_report_displays_only_the_selected_month(): void
    {
        $currentChild = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
            'full_name' => 'Anak Bulan Ini',
            'registered_at' => now()->startOfMonth()->toDateString(),
        ]);
        $previousChild = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
            'full_name' => 'Anak Bulan Lalu',
            'registered_at' => now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
        ]);

        $currentChild->examinations()->create([
            'posyandu_id' => $this->posyandu->id,
            'examined_at' => now()->startOfMonth()->toDateString(),
            'weight_kg' => 12.5,
            'height_cm' => 90.0,
        ]);
        $previousChild->examinations()->create([
            'posyandu_id' => $this->posyandu->id,
            'examined_at' => now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
            'weight_kg' => 11.0,
            'height_cm' => 85.0,
        ]);

        $this->currentChildImmunization($currentChild);

        $response = $this->actingAs($this->kader)
            ->get(route('kader.laporan.index', [
                'month' => now()->month,
                'year' => now()->year,
            ]));

        $response
            ->assertOk()
            ->assertSee('Laporan Posyandu')
            ->assertSee('Anak Bulan Ini')
            ->assertDontSee('Anak Bulan Lalu');

        $response->assertViewHas('stats', function (array $stats): bool {
            return $stats['registered'] === 2
                && $stats['examinations'] === 1
                && $stats['children_examined'] === 1
                && $stats['complete_records'] === 1
                && $stats['immunizations'] === 1;
        });
    }

    public function test_report_can_be_filtered_to_a_previous_month(): void
    {
        $currentChild = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
            'full_name' => 'Anak Bulan Ini',
        ]);
        $previousChild = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
            'full_name' => 'Anak Bulan Lalu',
        ]);

        $currentChild->examinations()->create([
            'posyandu_id' => $this->posyandu->id,
            'examined_at' => now()->startOfMonth()->toDateString(),
            'weight_kg' => 12.5,
            'height_cm' => 90.0,
        ]);
        $previousChild->examinations()->create([
            'posyandu_id' => $this->posyandu->id,
            'examined_at' => now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
            'weight_kg' => 11.0,
            'height_cm' => 85.0,
        ]);

        $previous = now()->subMonthNoOverflow();

        $this->actingAs($this->kader)
            ->get(route('kader.laporan.index', [
                'month' => $previous->month,
                'year' => $previous->year,
            ]))
            ->assertOk()
            ->assertSee('Anak Bulan Lalu')
            ->assertDontSee('Anak Bulan Ini');
    }

    public function test_report_csv_contains_period_data_and_admin_columns(): void
    {
        $child = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
            'full_name' => 'Anak CSV',
        ]);

        $child->examinations()->create([
            'posyandu_id' => $this->posyandu->id,
            'examined_at' => now()->startOfMonth()->toDateString(),
            'weight_kg' => 12.5,
            'height_cm' => 90.0,
            'head_circumference_cm' => 47.0,
            'notes' => 'Catatan pemeriksaan.',
            'recorded_by' => $this->kader->id,
        ]);

        $response = $this->actingAs($this->kader)->get(route('kader.laporan.csv', [
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $response->assertOk();
        $response->assertHeaderContains('content-type', 'text/csv');
        $response->assertDownload('laporan-posyandu-'.now()->format('Y-m').'.csv');

        $content = $response->streamedContent();

        $this->assertStringContainsString('Tanggal pemeriksaan', $content);
        $this->assertStringContainsString('Anak CSV', $content);
        $this->assertStringContainsString('12.5', $content);
        $this->assertStringContainsString('Catatan pemeriksaan.', $content);
    }

    public function test_printable_report_renders_administrative_summary(): void
    {
        $child = Balita::factory()->create([
            'posyandu_id' => $this->posyandu->id,
            'full_name' => 'Anak Cetak',
        ]);
        $child->examinations()->create([
            'posyandu_id' => $this->posyandu->id,
            'examined_at' => now()->startOfMonth()->toDateString(),
            'weight_kg' => 12.5,
            'height_cm' => 90.0,
        ]);

        $this->actingAs($this->kader)
            ->get(route('kader.laporan.print', [
                'month' => now()->month,
                'year' => now()->year,
            ]))
            ->assertOk()
            ->assertSee('Laporan bulanan')
            ->assertSee('Anak Cetak')
            ->assertSee('Rincian pemeriksaan');
    }

    public function test_invalid_report_period_falls_back_to_current_month(): void
    {
        $this->actingAs($this->kader)
            ->get(route('kader.laporan.index', ['month' => 99, 'year' => 1900]))
            ->assertOk()
            ->assertSee(bulan_id(now()->month).' '.now()->year);
    }

    public function test_parent_cannot_access_reports(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);

        $this->actingAs($parent)
            ->get(route('kader.laporan.index'))
            ->assertForbidden();

        $this->actingAs($parent)
            ->get(route('kader.laporan.csv'))
            ->assertForbidden();
    }

    private function currentChildImmunization(Balita $child): void
    {
        $child->immunizations()->create([
            'name' => 'Campak/MR',
            'administered_at' => now()->startOfMonth()->toDateString(),
            'status' => 'recorded',
            'recorded_by' => $this->kader->id,
        ]);
    }
}
