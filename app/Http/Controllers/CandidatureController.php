<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
    // Récupère toutes les candidatures avec leurs relations
    public function index()
    {
        $candidatures = Candidature::with('user', 'service', 'service.user')->get();
        return response()->json($candidatures);
    }

    // Crée une nouvelle candidature
    public function store(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        if (!$request->has(['service_id'])) {
            return response()->json(['error' => 'Données manquantes'], 400);
        }

        $validatedData = $request->validate([
            'service_id' => 'required|exists:services,id',
            'message' => 'nullable|string',
        ]);

        $user = User::find($userId);
        $roles = $user->roles()->pluck('name');

        if (!$roles->contains('postulant')) {
            $postulantRole = Role::where('name', 'postulant')->first();
            if ($postulantRole) {
                UserRole::updateOrCreate(
                    ['user_id' => $userId],
                    ['role_id' => $postulantRole->id]
                );
            }
        }

        $candidature = Candidature::create([
            'user_id' => $userId,
            'service_id' => $validatedData['service_id'],
            'message' => $validatedData['message'],
        ]);

        return response()->json([
            'message' => 'Candidature créée avec succès',
            'candidature' => $candidature
        ], 201);
    }

    // Affiche les détails d'une candidature spécifique
    public function show($id)
    {
        $candidature = Candidature::with('service', 'user', 'service.user')->findOrFail($id);
        return response()->json($candidature);
    }

    // Récupère les candidatures d'un utilisateur spécifique
    public function getCandidaturesByUser($userId)
    {
        $candidatures = Candidature::with('service', 'user', 'service.user.details', 'user.details')
                        ->where('user_id', $userId)
                        ->get();

        return response()->json($candidatures);
    }

    // Récupère les candidatures pour un service spécifique
    public function getCandidaturesByService($serviceId)
    {
        $candidatures = Candidature::with('service', 'user', 'service.user')
                        ->where('service_id', $serviceId)
                        ->get();

        return response()->json($candidatures);
    }

    // Récupère les candidatures pour tous les services postés par un utilisateur spécifique
    public function getCandidaturesByServiceOwner($serviceOwnerId)
    {
        $services = Service::where('user_id', $serviceOwnerId)->pluck('id');
        $candidatures = Candidature::with('service', 'user', 'service.user.details', 'user.details')
                        ->whereIn('service_id', $services)
                        ->get();

        return response()->json($candidatures);
    }

    // Met à jour les détails d'une candidature
    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string',
        ]);

        $candidature = Candidature::findOrFail($id);
        $candidature->update([
            'message' => $request->input('message'),
        ]);

        return response()->json([
            'message' => 'Candidature mise à jour avec succès',
            'candidature' => $candidature
        ]);
    }

    // Supprime une candidature
    public function destroy($id)
    {
        $candidature = Candidature::findOrFail($id);
        $candidature->delete();

        return response()->json(['message' => 'Candidature supprimée avec succès']);
    }

    // Change le statut d'une candidature
    public function changeStatus(Request $request, $id)
    {
        $candidature = Candidature::findOrFail($id);
        $validatedData = $request->validate([
            'status' => 'required|in:pending,accepted,rejected,canceled',
        ]);

        $candidature->status = $validatedData['status'];
        $candidature->save();

        return response()->json([
            'message' => 'Statut de la candidature mis à jour avec succès',
            'candidature' => $candidature
        ]);
    }

    // Compte le nombre de candidatures pour un service spécifique
    public function countCandidaturesByService($serviceId)
    {
        $count = Candidature::where('service_id', $serviceId)->count();
        return response()->json(['count' => $count]);
    }

    // Récupère toutes les candidatures avec un statut "pending"
    public function getPendingCandidatures()
    {
        $candidatures = Candidature::with('user', 'service')
                        ->where('status', 'pending')
                        ->get();

        return response()->json($candidatures);
    }

    // Filtre les candidatures selon différents critères
    public function filterCandidatures(Request $request)
    {
        $query = Candidature::with('user', 'service');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }

        $candidatures = $query->get();
        return response()->json($candidatures);
    }
}
