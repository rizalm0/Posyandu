<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreParentRequest;
use App\Http\Requests\Api\UpdateParentRequest;
use App\Http\Resources\UserResource;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ParentApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::query()
                ->where('role', 'parent')
                ->withCount('children')
                ->orderBy('name')
                ->get()
        );
    }

    public function store(StoreParentRequest $request): JsonResponse
    {
        $posyandu = Posyandu::query()->orderBy('id')->firstOrFail();
        $data = $request->validated();

        $data['posyandu_id'] = $posyandu->id;
        $data['role'] = 'parent';
        $data['is_active'] = $data['is_active'] ?? true;

        $parent = User::create($data);

        return (new UserResource($parent))
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $parent): UserResource
    {
        abort_unless($parent->role === 'parent', 404);

        return new UserResource($parent->loadCount('children'));
    }

    public function update(UpdateParentRequest $request, User $parent): UserResource
    {
        abort_unless($parent->role === 'parent', 404);

        $data = $request->validated();

        if (array_key_exists('password', $data) && $data['password'] === null) {
            unset($data['password']);
        }

        $parent->update($data);

        return new UserResource($parent->refresh()->loadCount('children'));
    }

    public function destroy(User $parent): JsonResponse
    {
        abort_unless($parent->role === 'parent', 404);

        $parent->delete();

        return response()->json(['message' => 'Akun orang tua berhasil dihapus.']);
    }
}
