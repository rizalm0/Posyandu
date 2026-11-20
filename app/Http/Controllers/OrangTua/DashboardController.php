<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use App\Models\Examination;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $children = $user->verifiedChildren()
            ->with('posyandu')
            ->orderBy('full_name')
            ->get();

        $selectedChild = null;
        $latestExamination = null;
        $recentExaminations = collect();
        $immunizations = collect();
        $visitCount = 0;

        if ($children->isNotEmpty()) {
            $requestedId = (int) $request->query('anak');
            $selectedChild = $children->firstWhere('id', $requestedId) ?? $children->first();

            $latestExamination = Examination::query()
                ->where('balita_id', $selectedChild->id)
                ->latest('examined_at')
                ->latest('id')
                ->first();

            $recentExaminations = Examination::query()
                ->where('balita_id', $selectedChild->id)
                ->latest('examined_at')
                ->latest('id')
                ->limit(3)
                ->get();

            $immunizations = $selectedChild->immunizations()
                ->limit(3)
                ->get();

            $visitCount = Examination::where('balita_id', $selectedChild->id)->count();
        }

        return view('orang-tua.dashboard', [
            'children' => $children,
            'selectedChild' => $selectedChild,
            'latestExamination' => $latestExamination,
            'recentExaminations' => $recentExaminations,
            'immunizations' => $immunizations,
            'visitCount' => $visitCount,
            'navChild' => $selectedChild,
        ]);
    }
}
