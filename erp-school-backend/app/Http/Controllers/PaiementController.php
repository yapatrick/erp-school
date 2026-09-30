<?php

namespace App\Http\Controllers;

// ==========================================
// PaiementController
// ==========================================
namespace App\Http\Controllers\Api;

use App\Models\Paiement;
use App\Models\Inscription;

class PaiementController extends Controller
{
    /**
     * GET - Lister les paiements
     */
    public function index(Request $request)
    {
        $query = Paiement::with(['etudiant', 'inscription', 'typePaiement', 'caissier']);

        if ($request->has('etudiant_id')) {
            $query->where('id_etudiant', $request->etudiant_id);
        }

        if ($request->has('mode_paiement')) {
            $query->parMode($request->mode_paiement);
        }

        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->parPeriode($request->date_debut, $request->date_fin);
        }

        $paiements = $query->latest('created_at')->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $paiements->items(),
            'pagination' => [
                'total' => $paiements->total(),
                'per_page' => $paiements->perPage(),
                'current_page' => $paiements->currentPage()
            ]
        ]);
    }

    /**
     * POST - Enregistrer un paiement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_etudiant' => 'required|exists:etudiants,id_etudiant',
            'id_inscription' => 'nullable|exists:inscriptions,id_inscription',
            'id_type_paiement' => 'required|exists:types_paiement,id_type',
            'montant' => 'required|numeric|min:0.01',
            'mode_paiement' => 'required|in:Espèces,Chèque,Virement,Mobile Money',
            'reference' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $paiement = Paiement::create(array_merge(
            $request->all(),
            [
                'id_utilisateur_caisse' => auth()->user()->id_utilisateur,
                'numero_recu' => $this->generateReceiptNumber()
            ]
        ));

        // Mettre à jour le montant payé de l'inscription
        if ($paiement->id_inscription) {
            $inscription = Inscription::find($paiement->id_inscription);
            $inscription->montant_paye += $paiement->montant;
            $inscription->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Paiement enregistré',
            'data' => $paiement->load(['etudiant', 'typePaiement'])
        ], 201);
    }

    /**
     * GET - Paiements par étudiant
     */
    public function paiementsEtudiant($etudiantId)
    {
        $paiements = Paiement::where('id_etudiant', $etudiantId)
            ->with(['typePaiement', 'inscription'])
            ->latest('created_at')
            ->get();

        $total = $paiements->sum('montant');

        return response()->json([
            'success' => true,
            'data' => $paiements,
            'total_paye' => $total
        ]);
    }

    /**
     * GET - Rapport caisse
     */
    public function rapportCaisse(Request $request)
    {
        $dateDebut = $request->get('date_debut', now()->startOfMonth());
        $dateFin = $request->get('date_fin', now()->endOfMonth());

        $paiements = Paiement::parPeriode($dateDebut, $dateFin)
            ->with('typePaiement')
            ->get();

        $par_mode = $paiements->groupBy('mode_paiement')->map(function($items) {
            return $items->sum('montant');
        });

        $par_type = $paiements->groupBy('id_type_paiement')->map(function($items) {
            return [
                'montant' => $items->sum('montant'),
                'count' => $items->count()
            ];
        });

        return response()->json([
            'success' => true,
            'periode' => [
                'debut' => $dateDebut,
                'fin' => $dateFin
            ],
            'total_general' => $paiements->sum('montant'),
            'par_mode' => $par_mode,
            'par_type' => $par_type,
            'nombre_transactions' => $paiements->count()
        ]);
    }

    private function generateReceiptNumber()
    {
        return 'REC-' . date('Ymd') . '-' . str_pad(
            Paiement::whereDate('created_at', today())->count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}

