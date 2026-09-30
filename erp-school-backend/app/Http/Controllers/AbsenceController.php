<?php

namespace App\Http\Controllers;

// ==========================================
// AbsenceController
// ==========================================
namespace App\Http\Controllers\Api;

use App\Models\Absence;

class AbsenceController extends Controller
{
    /**
     * GET - Absences d'un étudiant
     */
    public function absencesEtudiant($etudiantId, Request $request)
    {
        $query = Absence::where('id_etudiant', $etudiantId)
            ->with(['enseignement', 'professeur']);

        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->parPeriode($request->date_debut, $request->date_fin);
        }

        if ($request->boolean('non_justifiees')) {
            $query->nonJustifiee();
        }

        $absences = $query->latest('date_absence')->get();

        return response()->json([
            'success' => true,
            'data' => $absences,
            'total_absences' => $absences->count(),
            'total_non_justifiees' => $absences->where('justifiee', false)->count()
        ]);
    }

    /**
     * POST - Enregistrer une absence
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_etudiant' => 'required|exists:etudiants,id_etudiant',
            'id_enseignement' => 'required|exists:enseignements,id_enseignement',
            'date_absence' => 'required|date',
            'type_absence' => 'required|in:Absence,Retard',
            'motif' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $absence = Absence::create(array_merge(
            $request->all(),
            ['id_professeur' => auth()->user()->id_utilisateur]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Absence enregistrée',
            'data' => $absence->load(['etudiant', 'enseignement'])
        ], 201);
    }

    /**
     * PUT - Justifier une absence
     */
    public function justifier(Request $request, $id)
    {
        $absence = Absence::find($id);

        if (!$absence) {
            return response()->json([
                'success' => false,
                'message' => 'Absence non trouvée'
            ], 404);
        }

        $absence->update([
            'justifiee' => true,
            'motif' => $request->get('motif', $absence->motif)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absence justifiée',
            'data' => $absence
        ]);
    }
}
