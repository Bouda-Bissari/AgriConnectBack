<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
    public function index()
{
    // Récupère toutes les candidatures avec leurs relations
    $candidatures = Candidature::with('user', 'service','service.user')->get();

    return response()->json($candidatures);
}

    public function store(Request $request)
    {
        // Valide la requête
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'message' => 'nullable|string',
        ]);

        // Crée la candidature
        $candidature = Candidature::create([
            'user_id' => Auth::id(),
            'service_id' => $request->input('service_id'),
            'message' => $request->input('message'),
        ]);

        return response()->json([
            'message' => 'Candidature créée avec succès',
            'candidature' => $candidature
        ], 201);
    }

    public function show($id)
    {
        // Récupère la candidature

        $candidature = Candidature::with('service')->findOrFail($id);
        // $candidature = Candidature::with('user', 'service','service.user')->get();

        return response()->json($candidature);
    }

    public function update(Request $request, $id)
    {
        // Valide la requête
        $request->validate([
            'message' => 'nullable|string',
        ]);

        // Récupère et met à jour la candidature
        $candidature = Candidature::findOrFail($id);
        // $this->authorize('update', $candidature);

        $candidature->update([
            'message' => $request->input('message'),
        ]);

        return response()->json([
            'message' => 'Candidature mise à jour avec succès',
            'candidature' => $candidature
        ]);
    }

    public function destroy($id)
    {
        // Récupère et supprime la candidature
        $candidature = Candidature::findOrFail($id);
        // $this->authorize('delete', $candidature);
        $candidature->delete();

        return response()->json(['message' => 'Candidature supprimée avec succès']);
    }
}
