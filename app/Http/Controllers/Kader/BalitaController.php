<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BalitaController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $posyandu = $user->posyandu;
        abort_if($posyandu === null, 403);

        $search = trim((string) $request->query('q'));
        $status = (string) $request->query('status', 'semua');

        $balitas = Balita::query()
            ->where('posyandu_id', $posyandu->id)
            ->where('is_active', true)
            ->with(['latestExamination', 'verifiedGuardians', 'posyandu'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('parent_name', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('full_name')
            ->get();

        $counts = [
            'semua' => $balitas->count(),
            'belum_diperiksa' => $balitas->filter(fn (Balita $balita) => $balita->is_overdue)->count(),
            'perlu_dilengkapi' => $balitas->filter(
                fn (Balita $balita) => ! $balita->is_overdue && (
                    ! $balita->has_verified_guardian
                    || $balita->latestExamination === null
                    || $balita->latestExamination->height_cm === null
                )
            )->count(),
        ];

        $filtered = match ($status) {
            'belum_diperiksa' => $balitas->filter(fn (Balita $balita) => $balita->is_overdue)->values(),
            'perlu_dilengkapi' => $balitas->filter(
                fn (Balita $balita) => ! $balita->is_overdue && (
                    ! $balita->has_verified_guardian
                    || $balita->latestExamination === null
                    || $balita->latestExamination->height_cm === null
                )
            )->values(),
            default => $balitas->values(),
        };

        $perPage = 8;
        $page = Paginator::resolveCurrentPage();
        $items = $filtered->forPage($page, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $items,
            $filtered->count(),
            $perPage,
            $page,
            [
                'path' => route('kader.balita.index'),
                'query' => $request->query(),
            ]
        );

        return view('kader.balita.index', [
            'balitas' => $paginated,
            'counts' => $counts,
            'search' => $search,
            'status' => $status,
            'posyandu' => $posyandu,
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Balita::class);

        return view('kader.balita.form', [
            'balita' => new Balita([
                'registered_at' => today(),
                'posyandu_id' => $request->user()->posyandu_id,
            ]),
            'mode' => 'create',
            'registrationNumber' => $this->nextRegistrationNumber($request->user()->posyandu_id),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Balita::class);

        $data = $this->validatedData($request);
        $data['posyandu_id'] = $request->user()->posyandu_id;
        $data['registration_number'] = $this->nextRegistrationNumber($request->user()->posyandu_id);
        $data['is_active'] = true;

        $balita = Balita::create($data);

        return redirect()
            ->route('kader.balita.show', $balita)
            ->with('success', 'Data balita berhasil ditambahkan.');
    }

    public function show(Request $request, Balita $balita): View
    {
        Gate::authorize('view', $balita);

        abort_unless($balita->posyandu_id === $request->user()->posyandu_id, 403);

        $balita->load([
            'posyandu',
            'guardians',
            'immunizations.recorder',
        ]);

        $examinations = $balita->examinations()
            ->with('recorder')
            ->orderBy('examined_at')
            ->orderBy('id')
            ->get();

        $parentAccounts = User::query()
            ->where('role', 'parent')
            ->where('is_active', true)
            ->where(fn ($query) => $query
                ->whereNull('posyandu_id')
                ->orWhere('posyandu_id', $balita->posyandu_id))
            ->orderBy('name')
            ->get();

        return view('kader.balita.show', [
            'balita' => $balita,
            'examinations' => $examinations,
            'latestExamination' => $examinations->last(),
            'parentAccounts' => $parentAccounts,
            'chartData' => [
                'labels' => $examinations->map(fn ($exam) => tanggal_singkat_id($exam->examined_at))->values(),
                'weight' => $examinations->map(fn ($exam) => $exam->weight_kg !== null ? (float) $exam->weight_kg : null)->values(),
                'height' => $examinations->map(fn ($exam) => $exam->height_cm !== null ? (float) $exam->height_cm : null)->values(),
            ],
        ]);
    }

    public function edit(Balita $balita): View
    {
        Gate::authorize('update', $balita);

        return view('kader.balita.form', [
            'balita' => $balita,
            'mode' => 'edit',
            'registrationNumber' => $balita->registration_number,
        ]);
    }

    public function update(Request $request, Balita $balita): RedirectResponse
    {
        Gate::authorize('update', $balita);

        $data = $this->validatedData($request, $balita);
        $balita->update($data);

        return redirect()
            ->route('kader.balita.show', $balita)
            ->with('success', 'Data balita berhasil diperbarui.');
    }

    private function validatedData(Request $request, ?Balita $balita = null): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'parent_name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:25'],
            'address' => ['nullable', 'string', 'max:500'],
            'registered_at' => ['required', 'date', 'before_or_equal:today'],
        ]);
    }

    private function nextRegistrationNumber(?int $posyanduId): string
    {
        $year = now()->format('Y');
        $prefix = 'PSD-'.$year.'-';
        $last = Balita::query()
            ->where('posyandu_id', $posyanduId)
            ->where('registration_number', 'like', $prefix.'%')
            ->orderByDesc('registration_number')
            ->value('registration_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        do {
            $candidate = $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $exists = Balita::where('registration_number', $candidate)->exists();
            $next++;
        } while ($exists);

        return $candidate;
    }
}
