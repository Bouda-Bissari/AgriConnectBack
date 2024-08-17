<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetailRequest;
use App\Http\Requests\UpdateDetailRequest;
use App\Models\Detail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $details = Detail::all();
        return response()->json($details);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDetailRequest $request
     * @return JsonResponse
     */
    // public function store(StoreDetailRequest $request): JsonResponse
    // {
    //     $validated = $request->validated();

    //     // Création des détails
    //     $detail = Detail::create($validated);

    //     return response()->json(['message' => 'Detail Created', 'detail' => $detail], 201);
    // }

    public function store(StoreDetailRequest $request): JsonResponse
{
    $validated = $request->validated();

    // Création des détails
    $detail = Detail::create($validated);

    // Mise à jour de l'utilisateur authentifié
    $user = Auth::user();
    $user->is_completed = true;

    return response()->json(['message' => 'Detail Created', 'detail' => $detail], 201);
}


    /**
     * Display the specified resource.
     *
     * @param Detail $detail
     * @return JsonResponse
     */
    public function show(Detail $detail): JsonResponse
    {
        return response()->json($detail);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDetailRequest $request
     * @param Detail $detail
     * @return JsonResponse
     */
    public function update(UpdateDetailRequest $request, Detail $detail): JsonResponse
    {
        $validated = $request->validated();

        // Mise à jour des attributs des détails
        $detail->update($validated);

        // Mise à jour de l'utilisateur authentifié
    $user = Auth::user();
    $user->is_completed = true;

        return response()->json(['message' => 'Detail Updated', 'detail' => $detail]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Detail $detail
     * @return JsonResponse
     */
    public function destroy(Detail $detail): JsonResponse
    {
        $detail->delete();

        return response()->json(['message' => 'Detail Deleted']);
    }
}
