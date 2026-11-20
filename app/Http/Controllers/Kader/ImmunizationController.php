<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use App\Models\Immunization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ImmunizationController extends Controller
{
    public function store(Request $request, Balita $balita): RedirectResponse
    {
        Gate::authorize('create', [Immunization::class, $balita]);

        $data = $this->validatedData($request);

        $balita->immunizations()->create([
            ...$data,
            'recorded_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('kader.balita.show', $balita)
            ->with('success', 'Catatan imunisasi berhasil ditambahkan.');
    }

    public function edit(Immunization $immunization): View
    {
        Gate::authorize('update', $immunization);

        return view('kader.immunizations.form', [
            'immunization' => $immunization,
            'balita' => $immunization->balita,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Immunization $immunization): RedirectResponse
    {
        Gate::authorize('update', $immunization);

        $immunization->update($this->validatedData($request));

        return redirect()
            ->route('kader.balita.show', $immunization->balita_id)
            ->with('success', 'Catatan imunisasi berhasil diperbarui.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'administered_at' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', Rule::in(['recorded', 'needs_review'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
