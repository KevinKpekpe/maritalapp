<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\ReceptionTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function index(): View
    {
        $guests = Guest::with(['table' => fn ($query) => $query->withTrashed()])
            ->withTrashed()
            ->orderBy('primary_first_name')
            ->get();

        return view('guests.index', compact('guests'));
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('query'));

        $guestsQuery = Guest::with(['table' => fn ($q) => $q->withTrashed()])
            ->withTrashed();

        if ($query !== '') {
            $guestsQuery->where(function ($subQuery) use ($query) {
                $subQuery->where('primary_first_name', 'like', "%{$query}%")
                    ->orWhere('secondary_first_name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhereHas('table', function ($tableQuery) use ($query) {
                        $tableQuery->where('name', 'like', "%{$query}%");
                    });
            });
        }

        $guests = $guestsQuery->orderBy('primary_first_name')->get();

        $html = view('guests.partials.table', ['guests' => $guests])->render();

        return response()->json([
            'html' => $html,
            'count' => $guests->count(),
        ]);
    }

    public function create(): View
    {
        return view('guests.form', [
            'guest' => new Guest(),
            'tables' => $this->availableTables(),
        ]);
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
        return view('guests.form', [
            'guest' => $guest,
            'tables' => $this->availableTables($guest->reception_table_id),
        ]);
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

    protected function validateData(Request $request): array
    {
        $validated = $request->validate([
            'reception_table_id' => ['required', 'exists:reception_tables,id'],
            'type' => ['required', 'in:solo,couple'],
            'primary_first_name' => ['required', 'string', 'max:100'],
            'secondary_first_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
        ]);

        if ($validated['type'] === 'solo') {
            $validated['secondary_first_name'] = null;
        } else {
            $request->validate([
                'secondary_first_name' => ['required', 'string', 'max:100'],
            ]);
        }

        return $validated;
    }

    protected function availableTables()
    {
        return ReceptionTable::withTrashed()
            ->orderBy('name')
            ->get();
    }
}
