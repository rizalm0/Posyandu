<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class GrowthController extends Controller
{
    public function __invoke(Request $request, Balita $balita): View
    {
        Gate::authorize('view', $balita);

        $examinations = $balita->examinations()
            ->orderBy('examined_at')
            ->orderBy('id')
            ->get();

        $latest = $examinations->last();
        $children = $request->user()->verifiedChildren()->orderBy('full_name')->get();

        return view('orang-tua.growth', [
            'balita' => $balita,
            'examinations' => $examinations,
            'latestExamination' => $latest,
            'children' => $children,
            'navChild' => $balita,
            'chartData' => [
                'labels' => $examinations->map(fn ($exam) => tanggal_singkat_id($exam->examined_at))->values(),
                'weight' => $examinations->map(fn ($exam) => $exam->weight_kg !== null ? (float) $exam->weight_kg : null)->values(),
                'height' => $examinations->map(fn ($exam) => $exam->height_cm !== null ? (float) $exam->height_cm : null)->values(),
            ],
        ]);
    }
}
