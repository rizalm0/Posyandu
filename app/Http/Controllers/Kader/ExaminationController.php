<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use App\Models\Examination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Throwable;

class ExaminationController extends Controller
{
    public function create(Balita $balita): View
    {
        Gate::authorize('create', [Examination::class, $balita]);

        $latest = $balita->examinations()->latest('examined_at')->latest('id')->first();

        return view('kader.examinations.form', [
            'balita' => $balita,
            'examination' => new Examination([
                'examined_at' => today(),
            ]),
            'latestExamination' => $latest,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request, Balita $balita): RedirectResponse
    {
        Gate::authorize('create', [Examination::class, $balita]);

        $data = $this->validatedData($request);

        try {
            $examination = DB::transaction(function () use ($request, $balita, $data) {
                return Examination::create([
                    ...$data,
                    'balita_id' => $balita->id,
                    'posyandu_id' => $balita->posyandu_id,
                    'recorded_by' => $request->user()->id,
                ]);
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Pemeriksaan belum tersimpan. Periksa koneksi lalu coba lagi.');
        }

        return redirect()->route('kader.pemeriksaan.success', $examination);
    }

    public function success(Examination $examination): View
    {
        Gate::authorize('view', $examination);

        $examination->load(['balita', 'recorder']);

        return view('kader.examinations.success', [
            'examination' => $examination,
            'balita' => $examination->balita,
        ]);
    }

    public function edit(Examination $examination): View
    {
        Gate::authorize('update', $examination);

        $examination->load('balita');
        $latest = $examination->balita->examinations()
            ->where('id', '!=', $examination->id)
            ->latest('examined_at')
            ->latest('id')
            ->first();

        return view('kader.examinations.form', [
            'balita' => $examination->balita,
            'examination' => $examination,
            'latestExamination' => $latest,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Examination $examination): RedirectResponse
    {
        Gate::authorize('update', $examination);

        $data = $this->validatedData($request);

        try {
            DB::transaction(function () use ($examination, $data) {
                $examination->update($data);
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Perubahan pemeriksaan belum tersimpan. Periksa koneksi lalu coba lagi.');
        }

        return redirect()
            ->route('kader.balita.show', $examination->balita_id)
            ->with('success', 'Data pemeriksaan berhasil diperbarui.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'examined_at' => ['required', 'date', 'before_or_equal:today'],
            'weight_kg' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'height_cm' => ['required', 'numeric', 'min:1', 'max:200'],
            'head_circumference_cm' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
