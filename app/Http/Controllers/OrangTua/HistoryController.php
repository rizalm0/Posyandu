<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __invoke(Request $request, Balita $balita): View
    {
        Gate::authorize('view', $balita);

        $examinations = $balita->examinations()
            ->latest('examined_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $children = $request->user()->verifiedChildren()->orderBy('full_name')->get();

        return view('orang-tua.history', [
            'balita' => $balita,
            'examinations' => $examinations,
            'children' => $children,
            'navChild' => $balita,
        ]);
    }
}
