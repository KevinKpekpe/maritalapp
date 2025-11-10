<?php

namespace App\Http\Controllers;

use App\Models\Beverage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BeverageController extends Controller
{
    public function index(): View
    {
        $beverages = Beverage::orderBy('name')->get();

        return view('beverages.index', compact('beverages'));
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('query'));

        $beveragesQuery = Beverage::query();

        if ($query !== '') {
            $beveragesQuery->where(function ($subQuery) use ($query) {
                $subQuery->where('name', 'like', "%{$query}%")
                    ->orWhere('category', 'like', "%{$query}%");
            });
        }

        $beverages = $beveragesQuery->orderBy('name')->get();

        $html = view('beverages.partials.table', ['beverages' => $beverages])->render();

        return response()->json([
            'html' => $html,
            'count' => $beverages->count(),
        ]);
    }

    public function create(): View
    {
        return view('beverages.form', ['beverage' => new Beverage()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        Beverage::create($data);

        return redirect()->route('beverages.index')->with('status', 'Boisson ajoutée.');
    }

    public function edit(Beverage $beverage): View
    {
        return view('beverages.form', compact('beverage'));
    }

    public function update(Request $request, Beverage $beverage): RedirectResponse
    {
        $data = $this->validateData($request, $beverage->id);

        $beverage->update($data);

        return redirect()->route('beverages.index')->with('status', 'Boisson mise à jour.');
    }

    public function destroy(Beverage $beverage): RedirectResponse
    {
        $beverage->delete();

        return redirect()->route('beverages.index')->with('status', 'Boisson supprimée.');
    }

    protected function validateData(Request $request, ?int $beverageId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('beverages')->ignore($beverageId)],
            'category' => ['required', Rule::in(['alcool', 'sucre'])],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
