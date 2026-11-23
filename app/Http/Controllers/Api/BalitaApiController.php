<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBalitaRequest;
use App\Http\Requests\Api\UpdateBalitaRequest;
use App\Http\Resources\BalitaResource;
use App\Models\Balita;
use App\Models\Posyandu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BalitaApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return BalitaResource::collection(
            Balita::query()->orderBy('full_name')->get()
        );
    }

    public function store(StoreBalitaRequest $request): JsonResponse
    {
        $posyandu = Posyandu::query()->orderBy('id')->firstOrFail();
        $data = $request->validated();

        $data['posyandu_id'] = $posyandu->id;
        $data['registration_number'] = $this->nextRegistrationNumber($posyandu->id);
        $data['registered_at'] = $data['registered_at'] ?? today();
        $data['is_active'] = $data['is_active'] ?? true;

        $balita = Balita::create($data);

        return (new BalitaResource($balita))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Balita $balita): BalitaResource
    {
        return new BalitaResource($balita);
    }

    public function update(UpdateBalitaRequest $request, Balita $balita): BalitaResource
    {
        $balita->update($request->validated());

        return new BalitaResource($balita->refresh());
    }

    public function destroy(Balita $balita): JsonResponse
    {
        $balita->delete();

        return response()->json(['message' => 'Data balita berhasil dihapus.']);
    }

    private function nextRegistrationNumber(int $posyanduId): string
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
