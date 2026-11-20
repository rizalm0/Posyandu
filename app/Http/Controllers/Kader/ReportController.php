<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use App\Models\Examination;
use App\Models\Immunization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $data = $this->reportData($request);

        return view('kader.reports.index', $data);
    }

    public function print(Request $request): View
    {
        $data = $this->reportData($request);

        return view('kader.reports.print', $data);
    }

    public function csv(Request $request): StreamedResponse
    {
        $data = $this->reportData($request);
        $examinations = $data['examinations'];
        $period = $data['period'];

        $filename = 'laporan-posyandu-'.$period['start']->format('Y-m').'.csv';

        return response()->streamDownload(function () use ($examinations) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Tanggal pemeriksaan',
                'Nama balita',
                'Jenis kelamin',
                'Tanggal lahir',
                'Berat badan (kg)',
                'Tinggi/panjang badan (cm)',
                'Lingkar kepala (cm)',
                'Dicatat oleh',
                'Catatan',
            ]);

            foreach ($examinations as $examination) {
                fputcsv($handle, [
                    $examination->examined_at->format('Y-m-d'),
                    $examination->balita->full_name,
                    $examination->balita->gender_label,
                    $examination->balita->date_of_birth->format('Y-m-d'),
                    $examination->weight_kg,
                    $examination->height_cm,
                    $examination->head_circumference_cm,
                    $examination->recorder?->name ?? '-',
                    Str::limit((string) $examination->notes, 500),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function reportData(Request $request): array
    {
        $user = $request->user();
        $posyandu = $user->posyandu;
        abort_if($posyandu === null, 403);

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        if ($year < 2020 || $year > ((int) now()->format('Y')) + 1) {
            $year = now()->year;
        }

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();

        $balitas = Balita::query()
            ->where('posyandu_id', $posyandu->id)
            ->where('is_active', true)
            ->get();

        $newBalitas = $balitas->filter(
            fn (Balita $balita) => $balita->registered_at->between($start, $end)
        );

        $examinations = Examination::query()
            ->where('posyandu_id', $posyandu->id)
            ->whereBetween('examined_at', [$start->toDateString(), $end->toDateString()])
            ->with(['balita', 'recorder'])
            ->orderBy('examined_at')
            ->orderBy('id')
            ->get();

        $recap = $examinations
            ->groupBy(fn (Examination $examination) => $examination->examined_at->toDateString())
            ->map(function ($items, $date) {
                return [
                    'date' => Carbon::parse($date),
                    'total' => $items->count(),
                    'children' => $items->pluck('balita.full_name')->unique()->values(),
                    'complete' => $items->filter(fn (Examination $examination) => $examination->is_complete)->count(),
                ];
            })
            ->values()
            ->sortBy(fn ($row) => $row['date'])
            ->values();

        $immunizationCount = Immunization::query()
            ->whereHas('balita', fn ($query) => $query->where('posyandu_id', $posyandu->id))
            ->whereBetween('administered_at', [$start->toDateString(), $end->toDateString()])
            ->count();

        $years = range(max(2024, (int) now()->format('Y') - 2), (int) now()->format('Y'));

        return [
            'posyandu' => $posyandu,
            'period' => [
                'month' => $month,
                'year' => $year,
                'start' => $start,
                'end' => $end,
                'label' => bulan_id($month).' '.$year,
            ],
            'months' => collect(range(1, 12))->mapWithKeys(fn ($value) => [$value => bulan_id($value)]),
            'years' => $years,
            'stats' => [
                'registered' => $balitas->count(),
                'new_registrations' => $newBalitas->count(),
                'examinations' => $examinations->count(),
                'children_examined' => $examinations->pluck('balita_id')->unique()->count(),
                'complete_records' => $examinations->filter(fn (Examination $examination) => $examination->is_complete)->count(),
                'immunizations' => $immunizationCount,
            ],
            'recap' => $recap,
            'examinations' => $examinations,
            'generatedAt' => now(),
        ];
    }
}
