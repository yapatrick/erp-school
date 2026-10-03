<?php

namespace App\Http\Controllers;

// ==========================================
// NoteController
// ==========================================

use App\Models\Note;

class NoteController extends Controller
{
    /**
     * GET - Notes d'un étudiant
     */
    public function noteEtudiant($etudiantId, Request $request)
    {
        $query = Note::where('id_etudiant', $etudiantId)
            ->with(['matiere', 'professeur', 'periode']);

        if ($request->has('periode_id')) {
            $query->where('id_periode', $request->periode_id);
        }

        if ($request->has('type_evaluation')) {
            $query->where('type_evaluation', $request->type_evaluation);
        }

        $notes = $query->latest('date_evaluation')->get();

        // Calculer la moyenne
        $moyenne = $notes->count() > 0 
            ? $notes->avg('note_sur_vingt') 
            : 0;

        return response()->json([
            'success' => true,
            'data' => $notes,
            'moyenne' => round($moyenne, 2)
        ]);
    }

    /**
     * POST - Ajouter une note
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_etudiant' => 'required|exists:etudiants,id_etudiant',
            'id_matiere' => 'required|exists:matieres,id_matiere',
            'id_periode' => 'required|exists:periodes_evaluation,id_periode',
            'type_evaluation' => 'required|in:Devoir,Interrogation,Examen,Composition',
            'note' => 'required|numeric|min:0',
            'note_sur' => 'required|numeric|min:1',
            'observations' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $note = Note::create(array_merge(
            $request->all(),
            ['id_professeur' => auth()->user()->id_utilisateur]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Note enregistrée',
            'data' => $note->load(['etudiant', 'matiere', 'periode'])
        ], 201);
    }

    /**
     * PUT - Mettre à jour une note
     */
    public function update(Request $request, $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note non trouvée'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'note' => 'sometimes|numeric|min:0',
            'note_sur' => 'sometimes|numeric|min:1',
            'observations' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $note->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Note mise à jour',
            'data' => $note
        ]);
    }

    /**
     * GET - Bulletin d'une période
     */
    public function bulletinPeriode($etudiantId, $periodeId)
    {
        $notes = Note::where('id_etudiant', $etudiantId)
            ->where('id_periode', $periodeId)
            ->with(['matiere', 'professeur'])
            ->get();

        $bulletinParMatiere = $notes->groupBy('id_matiere')->map(function($notesMatiere) {
            $matiere = $notesMatiere->first()->matiere;
            $moyenne = $notesMatiere->avg('note_sur_vingt');

            return [
                'matiere' => $matiere->nom_matiere,
                'coefficient' => $matiere->coefficient,
                'moyenne' => round($moyenne, 2),
                'notes' => $notesMatiere->makeVisible(['note_sur_vingt', 'pourcentage'])
            ];
        });

        // Moyenne générale
        $moyenneGenerale = $bulletinParMatiere->avg('moyenne');

        return response()->json([
            'success' => true,
            'etudiant_id' => $etudiantId,
            'periode_id' => $periodeId,
            'bulletin' => $bulletinParMatiere,
            'moyenne_generale' => round($moyenneGenerale, 2)
        ]);
    }
}

