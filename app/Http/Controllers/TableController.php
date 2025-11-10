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

        return view('tables.index', compact('tables'));
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
        return view('tables.form', [
            'table' => new ReceptionTable(),
        ]);
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
        return view('tables.form', compact('table'));
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
