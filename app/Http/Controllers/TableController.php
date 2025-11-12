<?php

namespace App\Http\Controllers;

use App\Models\ReceptionTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    /**
     * Liste les tables de réception.
     */
    public function index(): View
    {
        $tables = ReceptionTable::withTrashed()
            ->orderBy('name')
            ->get();

        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Tables', 'url' => route('tables.index')],
        ];

        return view('tables.index', compact('tables', 'breadcrumbs'))->with('pageTitle', 'Tables');
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('query'));

        $tablesQuery = ReceptionTable::withTrashed();

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

        return redirect()->route('tables.index')->with('status', 'Table restaurée.');
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
