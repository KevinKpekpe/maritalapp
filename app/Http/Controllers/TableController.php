<?php

namespace App\Http\Controllers;

use App\Models\ReceptionTable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TableController extends Controller
{
    /**
     * Liste les tables de réception.
     */
    public function index(): View
    {
        $tables = ReceptionTable::whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Tables', 'url' => route('tables.index')],
        ];

        return view('tables.index', compact('tables', 'breadcrumbs'))->with('pageTitle', 'Tables');
    }

    /**
     * Affiche la corbeille (tables archivées).
     */
    public function trash(): View
    {
        $tables = ReceptionTable::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->get();

        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Tables', 'url' => route('tables.index')],
            ['label' => 'Corbeille', 'url' => route('tables.trash')],
        ];

        return view('tables.trash', compact('tables', 'breadcrumbs'))->with('pageTitle', 'Corbeille - Tables');
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('query'));

        $tablesQuery = ReceptionTable::whereNull('deleted_at');

        if ($query !== '') {
            $tablesQuery->where(function ($subQuery) use ($query) {
                $subQuery->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        }

        $tables = $tablesQuery->orderBy('name')->get();

        $html = view('tables.partials.table', ['tables' => $tables])->render();

        return response()->json([
            'html' => $html,
            'count' => $tables->count(),
        ]);
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Tables', 'url' => route('tables.index')],
            ['label' => 'Ajouter une table', 'url' => route('tables.create')],
        ];

        return view('tables.form', [
            'table' => new ReceptionTable(),
            'breadcrumbs' => $breadcrumbs,
        ])->with('pageTitle', 'Ajouter une table');
    }

    /**
     * Enregistre une table.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        ReceptionTable::create($data);

        return redirect()->route('tables.index')->with('status', 'Table créée avec succès.');
    }

    /**
     * Formulaire d’édition.
     */
    public function edit(ReceptionTable $table): View
    {
        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Tables', 'url' => route('tables.index')],
            ['label' => 'Modifier la table', 'url' => route('tables.edit', $table)],
        ];

        return view('tables.form', compact('table', 'breadcrumbs'))->with('pageTitle', 'Modifier la table');
    }

    /**
     * Met à jour une table.
     */
    public function update(Request $request, ReceptionTable $table): RedirectResponse
    {
        $data = $this->validateData($request);

        $table->update($data);

        return redirect()->route('tables.index')->with('status', 'Table mise à jour.');
    }

    /**
     * Supprime (soft delete) une table.
     */
    public function destroy(ReceptionTable $table): RedirectResponse
    {
        $table->update(['is_active' => false]);
        $table->delete();

        return redirect()->route('tables.index')->with('status', 'Table archivée.');
    }

    /**
     * Restaure une table supprimée.
     */
    public function restore(int $id): RedirectResponse
    {
        $table = ReceptionTable::withTrashed()->findOrFail($id);
        $table->restore();
        $table->update(['is_active' => true]);

        return redirect()->route('tables.trash')->with('status', 'Table restaurée.');
    }

    /**
     * Supprime définitivement une table.
     * On empêche la suppression si des invités (même archivés) sont encore liés.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $table = ReceptionTable::withTrashed()->findOrFail($id);

        // Vérifier la présence d'invités liés (actifs ou archivés)
        $hasGuests = $table->guests()->withTrashed()->exists();
        if ($hasGuests) {
            return redirect()
                ->route('tables.trash')
                ->with('error', 'Impossible de supprimer définitivement une table qui a encore des invités (même archivés).');
        }

        $table->forceDelete();

        return redirect()
            ->route('tables.trash')
            ->with('status', 'Table supprimée définitivement.');
    }

    /**
     * Exporte la liste des tables en fichier PDF avec noms numérotés.
     */
    public function export()
    {
        $filename = 'tables_'.now()->format('Y-m-d_His').'.pdf';

        // Récupérer toutes les tables, triées par nom
        $tables = ReceptionTable::withTrashed()
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('tables.export-pdf', [
            'tables' => $tables,
            'title' => 'Liste des tables',
        ]);

        return $pdf->download($filename);
    }

    /**
     * Télécharge un modèle CSV pour l'import.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $filename = 'modele_import_tables.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // En-têtes UTF-8 avec BOM pour Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes du CSV
            fputcsv($handle, [
                'Nom',
                'Description',
                'Active',
            ], ';');

            // Ajouter quelques exemples
            fputcsv($handle, [
                'Table 1',
                'Table près de la scène',
                'Oui',
            ], ';');

            fputcsv($handle, [
                'Table 2',
                'Table centrale',
                'Oui',
            ], ';');

            fputcsv($handle, [
                'Table 3',
                'Table près de l\'entrée',
                'Non',
            ], ';');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Affiche le formulaire d'import des tables.
     */
    public function showImport(): View
    {
        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Tables', 'url' => route('tables.index')],
            ['label' => 'Importer des tables', 'url' => route('tables.import.show')],
        ];

        return view('tables.import', compact('breadcrumbs'))->with('pageTitle', 'Importer des tables');
    }

    /**
     * Importe des tables depuis un fichier CSV.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'], // 10MB max
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Lire la première ligne (en-têtes)
        $headers = fgetcsv($handle, 0, ';');
        if ($headers === false) {
            return redirect()
                ->route('tables.import.show')
                ->with('error', 'Le fichier CSV est vide ou invalide.');
        }

        // Normaliser les en-têtes (supprimer BOM UTF-8 si présent)
        $headers = array_map(function ($header) {
            return trim(str_replace("\xEF\xBB\xBF", '', $header));
        }, $headers);

        // Mapping des colonnes attendues
        $columnMap = [
            'id' => null,
            'nom' => null,
            'name' => null,
            'description' => null,
            'active' => null,
            'is_active' => null,
        ];

        // Trouver les indices des colonnes
        foreach ($headers as $index => $header) {
            $headerLower = mb_strtolower(trim($header));
            foreach ($columnMap as $key => $value) {
                if (str_contains($headerLower, $key)) {
                    $columnMap[$key] = $index;
                    break;
                }
            }
        }

        // Vérifier les colonnes obligatoires
        if ($columnMap['nom'] === null && $columnMap['name'] === null) {
            fclose($handle);
            return redirect()
                ->route('tables.import.show')
                ->with('error', 'Colonne "Nom" manquante dans le CSV.');
        }

        $imported = 0;
        $errors = [];
        $lineNumber = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $lineNumber++;

                if (count($row) < 2) {
                    continue; // Ignorer les lignes vides
                }

                // Extraire les données selon le mapping
                $name = trim($row[$columnMap['nom']] ?? $row[$columnMap['name']] ?? '');
                $description = trim($row[$columnMap['description']] ?? '');
                $isActive = trim($row[$columnMap['active']] ?? $row[$columnMap['is_active']] ?? 'Oui');

                // Validation basique
                if (empty($name)) {
                    $errors[] = "Ligne {$lineNumber}: Le nom est requis";
                    continue;
                }

                // Vérifier si la table existe déjà
                $existingTable = ReceptionTable::withTrashed()->where('name', $name)->first();
                if ($existingTable) {
                    $errors[] = "Ligne {$lineNumber}: La table '{$name}' existe déjà";
                    continue;
                }

                // Convertir is_active
                $isActiveBool = in_array(mb_strtolower($isActive), ['oui', 'yes', '1', 'true', 'actif', 'active']);

                // Créer la table
                try {
                    ReceptionTable::create([
                        'name' => $name,
                        'description' => ! empty($description) ? $description : null,
                        'is_active' => $isActiveBool,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Ligne {$lineNumber}: Erreur lors de la création - {$e->getMessage()}";
                }
            }

            DB::commit();
            fclose($handle);

            $message = "{$imported} table(s) importée(s) avec succès.";
            if (! empty($errors)) {
                $message .= ' '.count($errors).' erreur(s) rencontrée(s).';
            }

            return redirect()
                ->route('tables.index')
                ->with('status', $message)
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);

            return redirect()
                ->route('tables.import.show')
                ->with('error', 'Erreur lors de l\'import : '.$e->getMessage());
        }
    }

    /**
     * Validation commune.
     */
    protected function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['is_active'] = (bool) $validated['is_active'];

        return $validated;
    }
}
