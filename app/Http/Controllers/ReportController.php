<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = Report::with('user', 'service')->get();
        return response()->json($reports);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Optionnel si nécessaire
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Vérifie si l'utilisateur est authentifié
    if (!Auth::check()) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }

    // Récupère l'ID de l'utilisateur authentifié
    $userId = Auth::id();

    // Valide les données de la requête
    $validatedData = $request->validate([
        'service_id' => 'required|exists:services,id',
        'description' => 'nullable|string',
    ]);

    // Crée un nouveau signalement
    $report = Report::create([
        'user_id' => $userId,
        'service_id' => $validatedData['service_id'],
        'description' => $validatedData['description'],
    ]);

    // Retourne une réponse JSON indiquant le succès de l'opération
    return response()->json([
        'message' => 'Signalement créé avec succès',
        'report' => $report
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $report = Report::with('service', 'user')->findOrFail($id);
        return response()->json($report);
    }

    /**
     * Retrieve all reports for a specific user.
     */
    public function getReportsByUser($userId)
    {
        $reports = Report::with('service')
                        ->where('user_id', $userId)
                        ->get();

        return response()->json($reports);
    }

    /**
     * Retrieve all reports for a specific service.
     */
    public function getReportsByService($serviceId)
    {
        $reports = Report::with('user')
                        ->where('service_id', $serviceId)
                        ->get();

        return response()->json($reports);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string',
        ]);

        $report = Report::findOrFail($id);
        $report->update([
            'message' => $request->input('message'),
        ]);

        return response()->json([
            'message' => 'Signalement mis à jour avec succès',
            'report' => $report
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return response()->json(['message' => 'Signalement supprimé avec succès']);
    }

    /**
     * Count the number of reports for a specific service.
     */
    public function countReportsByService($serviceId)
    {
        $count = Report::where('service_id', $serviceId)->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Filter reports according to different criteria.
     */
    public function filterReports(Request $request)
    {
        $query = Report::with('user', 'service');

        if ($request->has('message')) {
            $query->where('message', 'like', '%' . $request->input('message') . '%');
        }

        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }

        $reports = $query->get();
        return response()->json($reports);
    }
}
