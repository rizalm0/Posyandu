<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use App\Models\Examination;
use App\Models\Immunization;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $posyandu = $user->posyandu;
        abort_if($posyandu === null, 403, 'Akun kader belum terhubung ke Posyandu.');

        $balitas = Balita::query()
            ->where('posyandu_id', $posyandu->id)
            ->where('is_active', true)
            ->with(['latestExamination', 'verifiedGuardians', 'posyandu'])
            ->orderBy('full_name')
            ->get();

        $currentMonthExams = Examination::query()
            ->where('posyandu_id', $posyandu->id)
            ->whereBetween('examined_at', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->count();

        $newRegistrations = $balitas->filter(
            fn (Balita $balita) => $balita->registered_at->between(now()->startOfMonth(), now()->endOfMonth())
        )->count();

        $overdueBalitas = $balitas->filter(fn (Balita $balita) => $balita->is_overdue)->values();
        $incompleteBalitas = $balitas
            ->filter(fn (Balita $balita) => ! $balita->is_overdue && (
                ! $balita->has_verified_guardian
                || $balita->latestExamination === null
                || $balita->latestExamination->height_cm === null
            ))
            ->values();

        $attentionBalitas = $overdueBalitas
            ->concat($incompleteBalitas)
            ->unique('id')
            ->take(3)
            ->values();

        $recentExaminations = Examination::query()
            ->where('posyandu_id', $posyandu->id)
            ->with(['balita', 'recorder'])
            ->latest('examined_at')
            ->latest('id')
            ->limit(4)
            ->get();

        $immunizationCount = Immunization::query()
            ->whereHas('balita', fn ($query) => $query->where('posyandu_id', $posyandu->id))
            ->whereBetween('administered_at', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->count();

        $hour = (int) now()->format('H');
        $greeting = match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };

        return view('kader.dashboard', [
            'posyandu' => $posyandu,
            'greeting' => $greeting,
            'stats' => [
                'balitas' => $balitas->count(),
                'exams_this_month' => $currentMonthExams,
                'new_registrations' => $newRegistrations,
                'immunizations_this_month' => $immunizationCount,
            ],
            'recentExaminations' => $recentExaminations,
            'attentionBalitas' => $attentionBalitas,
            'attentionCount' => $overdueBalitas->count() + $incompleteBalitas->count(),
            'overdueCount' => $overdueBalitas->count(),
            'incompleteCount' => $incompleteBalitas->count(),
        ]);
    }
}
