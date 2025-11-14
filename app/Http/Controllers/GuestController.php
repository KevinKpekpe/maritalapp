<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\ReceptionTable;
use App\Services\WhatsApp\UltraMsgService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuestController extends Controller
{
    public function index(): View
    {
        $guests = Guest::with(['table' => fn ($query) => $query->withTrashed()])
            ->withTrashed()
            ->orderByDesc('created_at')
            ->get();

        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Invités', 'url' => route('guests.index')],
        ];

        return view('guests.index', compact('guests', 'breadcrumbs'))->with('pageTitle', 'Invités');
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('query'));

        $guestsQuery = Guest::with(['table' => fn ($q) => $q->withTrashed()])
            ->withTrashed();

        if ($query !== '') {
            $guestsQuery->where(function ($subQuery) use ($query) {
                $subQuery->where('primary_first_name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhereHas('table', function ($tableQuery) use ($query) {
                        $tableQuery->where('name', 'like', "%{$query}%");
                    });
            });
        }

        $statusFilter = $request->string('rsvp_status')->value();
        if ($statusFilter === 'not_confirmed') {
            $guestsQuery->where(function ($subQuery) {
                $subQuery->whereNull('rsvp_status')
                    ->orWhere('rsvp_status', 'pending');
            });
        } elseif (in_array($statusFilter, ['confirmed', 'declined'], true)) {
            $guestsQuery->where('rsvp_status', $statusFilter);
        }

        $whatsappFilter = $request->string('whatsapp_status')->value();
        if ($whatsappFilter === 'not_sent') {
            $guestsQuery->whereNull('whatsapp_sent_at');
        } elseif ($whatsappFilter === 'sent') {
            $guestsQuery->whereNotNull('whatsapp_sent_at');
        }

        $typeFilter = $request->string('guest_type')->value();
        if (in_array($typeFilter, ['solo', 'couple'], true)) {
            $guestsQuery->where('type', $typeFilter);
        }

        $sort = $request->string('sort')->value();
        if ($sort === 'recent') {
            $guestsQuery->orderByDesc('created_at');
        } elseif ($sort === 'oldest') {
            $guestsQuery->orderBy('created_at');
        } else {
            $guestsQuery->orderBy('primary_first_name');
        }

        $guests = $guestsQuery->get();

        $html = view('guests.partials.table', ['guests' => $guests])->render();

        return response()->json([
            'html' => $html,
            'count' => $guests->count(),
        ]);
    }

    public function create(): View
    {
        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Invités', 'url' => route('guests.index')],
            ['label' => 'Ajouter un invité', 'url' => route('guests.create')],
        ];

        return view('guests.form', [
            'guest' => new Guest(),
            'tables' => $this->availableTables(),
            'breadcrumbs' => $breadcrumbs,
        ])->with('pageTitle', 'Ajouter un invité');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['invitation_token'] = Str::uuid()->toString();
        $data['rsvp_status'] = 'pending';

        Guest::create($data);

        return redirect()->route('guests.index')->with('status', 'Invité créé avec succès.');
    }

    public function edit(Guest $guest): View
    {
        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Invités', 'url' => route('guests.index')],
            ['label' => 'Modifier un invité', 'url' => route('guests.edit', $guest)],
        ];

        return view('guests.form', [
            'guest' => $guest,
            'tables' => $this->availableTables($guest->reception_table_id),
            'breadcrumbs' => $breadcrumbs,
        ])->with('pageTitle', 'Modifier un invité');
    }

    public function update(Request $request, Guest $guest): RedirectResponse
    {
        $data = $this->validateData($request, $guest->id);

        if (! $guest->invitation_token) {
            $data['invitation_token'] = Str::uuid()->toString();
        }

        $guest->update($data);

        return redirect()->route('guests.index')->with('status', 'Invité mis à jour.');
    }

    public function destroy(Guest $guest): RedirectResponse
    {
        $guest->delete();

        return redirect()->route('guests.index')->with('status', 'Invité archivé.');
    }

    public function restore(int $id): RedirectResponse
    {
        $guest = Guest::withTrashed()->findOrFail($id);
        $guest->restore();

        return redirect()->route('guests.index')->with('status', 'Invité restauré.');
    }

    public function sendInvitation(Request $request, Guest $guest, UltraMsgService $whatsAppService): RedirectResponse
    {
        try {
            $result = $whatsAppService->sendInvitation($guest);

            if ($result['sent']) {
                return redirect()
                    ->route('guests.index')
                    ->with('status', 'Invitation WhatsApp envoyée avec succès à '.$guest->display_name.'.');
            } else {
                $errorMessage = $result['response']['error'] ?? 'Erreur inconnue lors de l\'envoi de l\'invitation WhatsApp.';

                return redirect()
                    ->route('guests.index')
                    ->with('error', 'Échec de l\'envoi de l\'invitation WhatsApp à '.$guest->display_name.': '.$errorMessage);
            }
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('guests.index')
                ->with('error', 'Échec de l\'envoi: '.$e->getMessage());
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('guests.index')
                ->with('error', 'Configuration UltraMsg manquante: '.$e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('guests.index')
                ->with('error', 'Une erreur inattendue est survenue lors de l\'envoi de l\'invitation WhatsApp.');
        }
    }

    /**
     * Envoie des invitations WhatsApp à plusieurs invités sélectionnés.
     */
    public function sendBulkInvitations(Request $request, UltraMsgService $whatsAppService): RedirectResponse
    {
        $request->validate([
            'guest_ids' => ['required', 'array', 'min:1', 'max:100'],
            'guest_ids.*' => ['required', 'integer', 'exists:guests,id'],
        ]);

        $guestIds = $request->input('guest_ids');
        $guests = Guest::whereIn('id', $guestIds)
            ->whereNull('deleted_at')
            ->get();

        if ($guests->isEmpty()) {
            return redirect()
                ->route('guests.index')
                ->with('error', 'Aucun invité valide sélectionné.');
        }

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($guests as $guest) {
            try {
                $result = $whatsAppService->sendInvitation($guest);
                if ($result['sent']) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = $guest->display_name.': Échec de l\'envoi';
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = $guest->display_name.': '.$e->getMessage();
                report($e);
            }
        }

        $message = "Envoi terminé : {$successCount} succès";
        if ($errorCount > 0) {
            $message .= ", {$errorCount} échecs";
        }

        return redirect()
            ->route('guests.index')
            ->with('status', $message)
            ->with('bulk_errors', $errors);
    }

    protected function validateData(Request $request, ?int $guestId = null): array
    {
        $validated = $request->validate([
            'reception_table_id' => [
                'required',
                'exists:reception_tables,id',
                function ($attribute, $value, $fail) use ($guestId) {
                    // Vérifier si la table a déjà 10 invités
                    $table = ReceptionTable::find($value);
                    if ($table) {
                        $guestCount = Guest::where('reception_table_id', $value)
                            ->whereNull('deleted_at')
                            ->when($guestId, function ($query) use ($guestId) {
                                // Exclure l'invité actuel du comptage si on est en mode édition
                                $query->where('id', '!=', $guestId);
                            })
                            ->count();

                        if ($guestCount >= 10) {
                            $fail('Cette table a déjà atteint sa capacité maximale de 10 invités.');
                        }
                    }
                },
            ],
            'type' => ['required', 'in:solo,couple'],
            'primary_first_name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:30',
                function ($attribute, $value, $fail) {
                    $formattedPhone = UltraMsgService::formatPhone($value);
                    if ($formattedPhone === null) {
                        $fail('Le numéro de téléphone fourni est invalide.');
                    }
                },
            ],
            'email' => ['nullable', 'email', 'max:150'],
        ]);

        // Formater le numéro de téléphone (déjà validé, on peut le formater en toute sécurité)
        if (isset($validated['phone'])) {
            $validated['phone'] = UltraMsgService::formatPhone($validated['phone']);
        }

        return $validated;
    }

    /**
     * Exporte la liste des invités en fichier PDF avec noms numérotés.
     */
    public function export()
    {
        $filename = 'invites_'.now()->format('Y-m-d_His').'.pdf';

        // Récupérer tous les invités avec leur table, triés par prénom
        $guests = Guest::with(['table' => fn ($query) => $query->withTrashed()])
            ->orderBy('primary_first_name')
            ->get();

        $pdf = Pdf::loadView('guests.export-pdf', [
            'guests' => $guests,
            'title' => 'Liste des invités',
        ]);

        return $pdf->download($filename);
    }

    /**
     * Télécharge un modèle CSV pour l'import.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $filename = 'modele_import_invites.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // En-têtes UTF-8 avec BOM pour Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes du CSV
            fputcsv($handle, [
                'Type',
                'Prénom principal',
                'Téléphone',
                'Email',
                'Table',
            ], ';');

            // Ajouter quelques exemples
            fputcsv($handle, [
                'solo',
                'Jean',
                '+243 999 123 456',
                'jean@example.com',
                'Table 1',
            ], ';');

            fputcsv($handle, [
                'couple',
                'Marie & Pierre',
                '+33 1 23 45 67 89',
                'marie@example.com',
                'Table 2',
            ], ';');

            fputcsv($handle, [
                'solo',
                'Sophie',
                '',
                '+243 888 765 432',
                '',
                'Table 1',
            ], ';');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Affiche le formulaire d'import des invités.
     */
    public function showImport(): View
    {
        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Invités', 'url' => route('guests.index')],
            ['label' => 'Importer des invités', 'url' => route('guests.import.show')],
        ];

        return view('guests.import', compact('breadcrumbs'))->with('pageTitle', 'Importer des invités');
    }

    /**
     * Importe des invités depuis un fichier CSV.
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
                ->route('guests.import.show')
                ->with('error', 'Le fichier CSV est vide ou invalide.');
        }

        // Normaliser les en-têtes (supprimer BOM UTF-8 si présent)
        $headers = array_map(function ($header) {
            return trim(str_replace("\xEF\xBB\xBF", '', $header));
        }, $headers);

        // Mapping des colonnes attendues
        $columnMap = [
            'id' => null,
            'type' => null,
            'prénom principal' => null,
            'téléphone' => null,
            'phone' => null,
            'email' => null,
            'table' => null,
            'statut rsvp' => null,
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
        $requiredColumns = ['type', 'prénom principal', 'téléphone'];
        $missingColumns = [];
        foreach ($requiredColumns as $col) {
            if ($columnMap[$col] === null && ($col !== 'téléphone' || $columnMap['phone'] === null)) {
                $missingColumns[] = $col;
            }
        }

        if (! empty($missingColumns)) {
            fclose($handle);
            return redirect()
                ->route('guests.import.show')
                ->with('error', 'Colonnes manquantes dans le CSV : '.implode(', ', $missingColumns));
        }

        $imported = 0;
        $errors = [];
        $lineNumber = 1;

        // Récupérer toutes les tables pour le mapping
        $tables = ReceptionTable::all()->keyBy('name');

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $lineNumber++;

                if (count($row) < 3) {
                    continue; // Ignorer les lignes vides
                }

                // Extraire les données selon le mapping
                $type = trim($row[$columnMap['type']] ?? '');
                $primaryFirstName = trim($row[$columnMap['prénom principal']] ?? '');
                $phone = trim($row[$columnMap['téléphone']] ?? $row[$columnMap['phone']] ?? '');
                $email = trim($row[$columnMap['email']] ?? '');
                $tableName = trim($row[$columnMap['table']] ?? '');

                // Validation basique
                if (empty($primaryFirstName) || empty($phone)) {
                    $errors[] = "Ligne {$lineNumber}: Prénom principal et téléphone sont requis";
                    continue;
                }

                if (! in_array($type, ['solo', 'couple'])) {
                    $type = 'solo'; // Par défaut
                }

                // Formater le numéro de téléphone
                $formattedPhone = UltraMsgService::formatPhone($phone);
                if ($formattedPhone === null) {
                    $errors[] = "Ligne {$lineNumber}: Numéro de téléphone invalide ({$phone})";
                    continue;
                }

                // Trouver la table (obligatoire)
                $tableId = null;
                if (! empty($tableName)) {
                    $table = $tables->firstWhere('name', $tableName);
                    if ($table) {
                        // Vérifier si la table a déjà 10 invités
                        $guestCount = Guest::where('reception_table_id', $table->id)
                            ->whereNull('deleted_at')
                            ->count();

                        if ($guestCount >= 10) {
                            $errors[] = "Ligne {$lineNumber}: La table '{$tableName}' a déjà atteint sa capacité maximale de 10 invités";
                            continue;
                        }

                        $tableId = $table->id;
                    } else {
                        $errors[] = "Ligne {$lineNumber}: Table '{$tableName}' introuvable";
                        continue;
                    }
                } else {
                    // Si aucune table n'est spécifiée, utiliser la première table disponible (avec moins de 10 invités)
                    $availableTable = $tables->first(function ($table) {
                        $guestCount = Guest::where('reception_table_id', $table->id)
                            ->whereNull('deleted_at')
                            ->count();
                        return $guestCount < 10;
                    });

                    if ($availableTable) {
                        $tableId = $availableTable->id;
                    } else {
                        $errors[] = "Ligne {$lineNumber}: Aucune table disponible (toutes les tables ont atteint leur capacité maximale de 10 invités)";
                        continue;
                    }
                }

                // Créer l'invité
                try {
                    Guest::create([
                        'reception_table_id' => $tableId,
                        'type' => $type,
                        'primary_first_name' => $primaryFirstName,
                        'phone' => $formattedPhone,
                        'email' => ! empty($email) ? $email : null,
                        'invitation_token' => Str::uuid()->toString(),
                        'rsvp_status' => 'pending',
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Ligne {$lineNumber}: Erreur lors de la création - {$e->getMessage()}";
                }
            }

            DB::commit();
            fclose($handle);

            $message = "{$imported} invité(s) importé(s) avec succès.";
            if (! empty($errors)) {
                $message .= ' '.count($errors).' erreur(s) rencontrée(s).';
            }

            return redirect()
                ->route('guests.index')
                ->with('status', $message)
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);

            return redirect()
                ->route('guests.import.show')
                ->with('error', 'Erreur lors de l\'import : '.$e->getMessage());
        }
    }

    /**
     * Récupère les tables disponibles (qui ont moins de 10 invités).
     *
     * @param int|null $currentTableId ID de la table actuelle (pour l'édition, permet de garder la table même si elle est pleine)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function availableTables(?int $currentTableId = null)
    {
        $maxGuestsPerTable = 10;

        // Récupérer toutes les tables avec le nombre d'invités actifs
        $tables = ReceptionTable::withTrashed()
            ->withCount(['guests' => function ($query) {
                $query->whereNull('deleted_at');
            }])
            ->orderBy('name')
            ->get();

        // Filtrer les tables qui ont moins de 10 invités, ou la table actuelle si on est en mode édition
        return $tables->filter(function ($table) use ($maxGuestsPerTable, $currentTableId) {
            // Toujours inclure la table actuelle si on est en mode édition
            if ($currentTableId && $table->id === $currentTableId) {
                return true;
            }
            // Sinon, inclure seulement les tables avec moins de 10 invités
            return $table->guests_count < $maxGuestsPerTable;
        });
    }
}
