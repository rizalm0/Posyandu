<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ParentAccountController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $search = trim((string) $request->query('q'));

        $parents = User::query()
            ->where('role', 'parent')
            ->where(fn ($query) => $query
                ->whereNull('posyandu_id')
                ->orWhere('posyandu_id', $request->user()->posyandu_id))
            ->withCount('children')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('kader.parent-accounts.index', [
            'parents' => $parents,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('kader.parent-accounts.form', [
            'parent' => new User(['is_active' => true]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'posyandu_id' => $request->user()->posyandu_id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => 'parent',
            'is_active' => true,
        ]);

        return redirect()
            ->route('kader.orang-tua.index')
            ->with('success', 'Akun orang tua/wali berhasil dibuat. Sampaikan kredensial secara langsung kepada yang bersangkutan.');
    }

    public function edit(User $parent): View
    {
        Gate::authorize('update', $parent);

        return view('kader.parent-accounts.form', [
            'parent' => $parent,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, User $parent): RedirectResponse
    {
        Gate::authorize('update', $parent);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($parent->id)],
            'phone' => ['nullable', 'string', 'max:25'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $parent->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        if (! empty($data['password'])) {
            $parent->password = $data['password'];
        }

        $parent->save();

        return redirect()
            ->route('kader.orang-tua.index')
            ->with('success', 'Akun orang tua/wali berhasil diperbarui.');
    }
}
