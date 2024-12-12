<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Models\UserRole;
use App\Notifications\CandidatureNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
    // Récupère toutes les candidatures avec leurs relations
    public function index()
    {
        $candidatures = Candidature::with('user', 'service', 'service.user')
            ->where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc') // Trie par date de création, les plus récentes d'abord
            ->get();

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

        $user = User::with('details')->find($userId);  // Charger les détails de l'utilisateur
        $roles = $user->roles()->pluck('name');

        if (!$roles->contains('ouvrier')) {
            $postulantRole = Role::where('name', 'ouvrier')->first();
            if ($postulantRole) {
                UserRole::updateOrCreate(
                    ['user_id' => $userId],
                    ['role_id' => $postulantRole->id]
                );
            }
        }

        // Créer la candidature
        $candidature = Candidature::create([
            'user_id' => $userId,
            'service_id' => $validatedData['service_id'],
            'message' => $validatedData['message'],
        ]);

        // Charger les relations pour accéder au titre du service et à l'utilisateur qui a posté le service
        $candidature->load('service', 'service.user');

        // Récupérer l'email de l'utilisateur qui postule depuis la table `details`
        $applyingUserEmail = $user->details->email;

        // Préparer les détails de la notification pour l'utilisateur qui postule
        $applyingUserDetails = [
            'body' => 'Vous avez postulé avec succès à la candidature pour le service: ' . $candidature->service->title,
            'url' => url('http://localhost:5173/profil/user/candidature')
        ];

        // Préparer les détails de la notification pour l'utilisateur qui a posté le service
        $posterUserDetails = [
            'body' => 'Un utilisateur a postulé pour le service: ' . $candidature->service->title,
            'url' => url('http://localhost:5173/profil/user/candidature/' . $candidature->id)
        ];

        $roles = $user->roles()->pluck('name');

        // Envoyer la notification à l'utilisateur qui postule
        $user->notify(new CandidatureNotification($applyingUserDetails));

        // Envoyer la notification à l'utilisateur qui a posté le service
        $posterUser = $candidature->service->user;
        if ($posterUser) {
            $posterUser->notify(new CandidatureNotification($posterUserDetails));
        }

        return response()->json([
            'message' => 'Candidature soumise avec succès et notifications envoyées!',
            'candidature' => $candidature,
            'roles' => $roles
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
            ->where('status', '!=', 'deleted')
            ->orderBy('updated_at', 'desc') // Trie par date de mise à jour
            ->get();

        return response()->json($candidatures);
    }


    // Récupère les candidatures pour un service spécifique
    public function getCandidaturesByService($serviceId)
    {
        $candidatures = Candidature::with('service', 'user', 'service.user')
            ->where('service_id', $serviceId)
            ->where('status', '!=', 'deleted')
            ->orderBy('updated_at', 'desc') // Trie par date de mise à jour
            ->get();

        return response()->json($candidatures);
    }



    // Récupère les candidatures pour tous les services postés par un utilisateur spécifique
    public function getCandidaturesByServiceOwner($serviceOwnerId)
    {
        $services = Service::where('user_id', $serviceOwnerId)->pluck('id');
        $candidatures = Candidature::with('service', 'user', 'service.user.details', 'user.details')
            ->whereIn('service_id', $services)
            ->where('status', '!=', 'deleted')
            ->orderBy('updated_at', 'desc') // Trie par date de mise à jour
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
    // public function changeStatus(Request $request, $id)
    // {
    //     $candidature = Candidature::findOrFail($id);
    //     $validatedData = $request->validate([
    //         'status' => 'required|in:pending,accepted,rejected,canceled,deleted',
    //     ]);

    //     $candidature->status = $validatedData['status'];
    //     $candidature->save();

    //     return response()->json([
    //         'message' => 'Statut de la candidature mis à jour avec succès',
    //         'candidature' => $candidature
    //     ]);
    // }



    //     public function changeStatus(Request $request, $id)
    // {
    //     $candidature = Candidature::findOrFail($id);
    //     $validatedData = $request->validate([
    //         'status' => 'required|in:pending,accepted,rejected,canceled,deleted',
    //     ]);

    //     // Mettre à jour le statut de la candidature
    //     $candidature->status = $validatedData['status'];
    //     $candidature->save();

    //     // Préparer les détails de la notification pour le postulant
    //     $applicantNotificationDetails = [
    //         'body' => 'Le statut de votre candidature pour le service : ' . $candidature->service->title . ' a été mis à jour en : ' . $validatedData['status'],
    //         'url' => url('/candidatures/' . $candidature->id),
    //         'status' => $validatedData['status']
    //     ];

    //     // Envoyer la notification au postulant
    //     $candidature->user->notify(new CandidatureNotification($applicantNotificationDetails));

    //     // Préparer les détails de la notification pour le créateur du service
    //     $posterNotificationDetails = [
    //         'body' => 'Le statut de la candidature pour votre service : ' . $candidature->service->title . ' a été mis à jour en : ' . $validatedData['status'],
    //         'url' => url('/candidatures/' . $candidature->id),
    //         'status' => $validatedData['status']
    //     ];

    //     // Envoyer la notification au créateur du service
    //     $candidature->service->user->notify(new CandidatureNotification($posterNotificationDetails));

    //     return response()->json([
    //         'message' => 'Statut de la candidature mis à jour avec succès et notifications envoyées',
    //         'candidature' => $candidature
    //     ]);
    // }
    public function changeStatus(Request $request, $id)
    {
        $candidature = Candidature::findOrFail($id);
        $validatedData = $request->validate([
            'status' => 'required|in:pending,accepted,rejected,canceled,deleted',
        ]);

        // Mettre à jour le statut de la candidature
        $candidature->status = $validatedData['status'];
        $candidature->save();

        // Traductions des statuts en français
        $statusMessages = [
            'pending' => 'Votre candidature est en attente de traitement.',
            'accepted' => 'Félicitations, votre candidature a été acceptée !',
            'rejected' => 'Malheureusement, votre candidature a été rejetée.',
            'canceled' => 'Votre candidature a été annulée.',
            'deleted' => 'Votre candidature a été supprimée.'
        ];

        $statusLabels = [
            'pending' => 'en attente',
            'accepted' => 'acceptée',
            'rejected' => 'rejetée',
            'canceled' => 'annulée',
            'deleted' => 'supprimée'
        ];

        // Préparer les détails de la notification pour le postulant
        $applicantNotificationDetails = [
            'body' => $statusMessages[$validatedData['status']] . ' Service : ' . $candidature->service->title,
            'url' => url('/candidatures/' . $candidature->id),
            'status' => $statusLabels[$validatedData['status']] // Utiliser le statut en français
        ];

        // Envoyer la notification au postulant
        $candidature->user->notify(new CandidatureNotification($applicantNotificationDetails));

        // Préparer les détails de la notification pour le créateur du service
        $posterNotificationDetails = [
            'body' => 'Le statut de la candidature pour votre service : ' . $candidature->service->title . ' a été mis à jour en : ' . $statusLabels[$validatedData['status']] . ' par ' . $candidature->user->fullName,
            'url' => url('/candidatures/' . $candidature->id),
            'status' => $statusLabels[$validatedData['status']] // Utiliser le statut en français
        ];

        // Envoyer la notification au créateur du service
        $candidature->service->user->notify(new CandidatureNotification($posterNotificationDetails));

        return response()->json([
            'message' => 'Statut de la candidature mis à jour avec succès et notifications envoyées',
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
