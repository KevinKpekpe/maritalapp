<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Liste les utilisateurs.
     */
    public function index(): View
    {
        $users = User::withTrashed()
            ->orderBy('name')
            ->get();

        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Utilisateurs', 'url' => route('users.index')],
        ];

        return view('users.index', compact('users', 'breadcrumbs'))->with('pageTitle', 'Utilisateurs');
    }

    /**
     * Recherche d'utilisateurs.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('query'));

        $usersQuery = User::withTrashed();

        if ($query !== '') {
            $usersQuery->where(function ($subQuery) use ($query) {
                $subQuery->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });
        }

        $users = $usersQuery->orderBy('name')->get();

        $html = view('users.partials.table', ['users' => $users])->render();

        return response()->json([
            'html' => $html,
            'count' => $users->count(),
        ]);
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Utilisateurs', 'url' => route('users.index')],
            ['label' => 'Ajouter un utilisateur', 'url' => route('users.create')],
        ];

        return view('users.form', [
            'user' => new User(),
            'breadcrumbs' => $breadcrumbs,
        ])->with('pageTitle', 'Ajouter un utilisateur');
    }

    /**
     * Enregistre un utilisateur.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        // Le cast 'hashed' dans le modèle User gère automatiquement le hashage
        // Pas besoin de Hash::make() ici

        User::create($data);

        return redirect()->route('users.index')->with('status', 'Utilisateur créé avec succès.');
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(User $user): View
    {
        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Utilisateurs', 'url' => route('users.index')],
            ['label' => 'Modifier l\'utilisateur', 'url' => route('users.edit', $user)],
        ];

        return view('users.form', compact('user', 'breadcrumbs'))->with('pageTitle', 'Modifier l\'utilisateur');
    }

    /**
     * Met à jour un utilisateur.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validateData($request, $user->id);

        // Si un nouveau mot de passe est fourni, le cast 'hashed' dans le modèle User gère automatiquement le hashage
        if (empty($data['password'])) {
            // Sinon, ne pas modifier le mot de passe
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('status', 'Utilisateur mis à jour.');
    }

    /**
     * Supprime (soft delete) un utilisateur.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Empêcher la suppression de l'utilisateur connecté
        if (Auth::check() && $user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', 'Utilisateur archivé.');
    }

    /**
     * Restaure un utilisateur supprimé.
     */
    public function restore(int $id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')->with('status', 'Utilisateur restauré.');
    }

    /**
     * Validation commune.
     */
    protected function validateData(Request $request, ?int $userId = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
        ];

        // Le mot de passe est requis lors de la création, optionnel lors de la modification
        if ($userId === null) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $request->validate($rules);
    }
}

