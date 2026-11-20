<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuardianController extends Controller
{
    public function store(Request $request, Balita $balita): RedirectResponse
    {
        Gate::authorize('update', $balita);

        $data = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email')->where(fn ($query) => $query->where('role', 'parent')),
            ],
            'relationship' => ['required', Rule::in(['father', 'mother', 'guardian'])],
            'verify_now' => ['nullable', 'boolean'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $parent = User::where('email', $data['email'])->where('role', 'parent')->firstOrFail();

        if (! $parent->is_active) {
            return back()->withErrors(['email' => 'Akun orang tua tersebut sedang tidak aktif.']);
        }

        if ($parent->posyandu_id !== null && $parent->posyandu_id !== $balita->posyandu_id) {
            return back()->withErrors(['email' => 'Akun orang tua tersebut terdaftar di Posyandu lain.']);
        }

        DB::transaction(function () use ($balita, $parent, $data) {
            if ($data['is_primary'] ?? false) {
                $balita->guardians()->newPivotStatement()
                    ->where('balita_id', $balita->id)
                    ->update(['is_primary' => false]);
            }

            $balita->guardians()->syncWithoutDetaching([
                $parent->id => [
                    'relationship' => $data['relationship'],
                    'is_primary' => (bool) ($data['is_primary'] ?? false),
                    'verified_at' => ($data['verify_now'] ?? false) ? now() : null,
                ],
            ]);
        });

        return back()->with('success', 'Akun orang tua/wali berhasil dihubungkan.');
    }

    public function verify(Balita $balita, User $parent): RedirectResponse
    {
        Gate::authorize('update', $balita);

        abort_unless(
            $balita->guardians()->whereKey($parent->id)->exists(),
            404
        );

        abort_if(
            $parent->posyandu_id !== null && $parent->posyandu_id !== $balita->posyandu_id,
            403
        );

        $balita->guardians()->updateExistingPivot($parent->id, [
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Akses orang tua/wali berhasil diverifikasi.');
    }

    public function destroy(Balita $balita, User $parent): RedirectResponse
    {
        Gate::authorize('update', $balita);

        $balita->guardians()->detach($parent->id);

        return back()->with('success', 'Hubungan akun orang tua/wali telah dilepas.');
    }
}
