<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ImmunizationHistoryController extends Controller
{
    public function __invoke(Request $request, Balita $balita): View
    {
        Gate::authorize('view', $balita);

        $immunizations = $balita->immunizations()
            ->paginate(10)
            ->withQueryString();

        $children = $request->user()->verifiedChildren()->orderBy('full_name')->get();

        return view('orang-tua.immunizations', [
            'balita' => $balita,
            'immunizations' => $immunizations,
            'children' => $children,
            'navChild' => $balita,
        ]);
    }
}
