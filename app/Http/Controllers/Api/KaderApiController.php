<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreKaderRequest;
use App\Http\Requests\Api\UpdateKaderRequest;
use App\Http\Resources\UserResource;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KaderApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::query()
                ->where('role', 'kader')
                ->orderBy('name')
                ->get()
        );
    }

    public function store(StoreKaderRequest $request): JsonResponse
    {
        $posyandu = Posyandu::query()->orderBy('id')->firstOrFail();
        $data = $request->validated();

        $data['posyandu_id'] = $posyandu->id;
        $data['role'] = 'kader';
        $data['is_active'] = $data['is_active'] ?? true;

        $kader = User::create($data);

        return (new UserResource($kader))
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $kader): UserResource
    {
        abort_unless($kader->role === 'kader', 404);

        return new UserResource($kader);
    }

    public function update(UpdateKaderRequest $request, User $kader): UserResource
    {
        abort_unless($kader->role === 'kader', 404);

        $data = $request->validated();

        if (array_key_exists('password', $data) && $data['password'] === null) {
            unset($data['password']);
        }

        $kader->update($data);

        return new UserResource($kader->refresh());
    }

    public function destroy(User $kader): JsonResponse
    {
        abort_unless($kader->role === 'kader', 404);

        $kader->delete();

        return response()->json(['message' => 'Akun kader berhasil dihapus.']);
    }
}
