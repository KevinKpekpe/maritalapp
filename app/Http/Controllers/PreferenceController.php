<?php

namespace App\Http\Controllers;

use App\Models\Beverage;
use App\Models\GuestPreference;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreferenceController extends Controller
{
    public function index(): View
    {
        $data = $this->buildData();

        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => url('/')],
            ['label' => 'Préférences', 'url' => route('preferences.index')],
        ];

        return view('preferences.index', array_merge($data, ['breadcrumbs' => $breadcrumbs]))->with('pageTitle', 'Préférences');
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('query'));

        $data = $this->buildData($query);

        $tableHtml = view('preferences.partials.table', $data)->render();
        $summaryHtml = view('preferences.partials.summary', $data)->render();

        return response()->json([
            'table_html' => $tableHtml,
            'summary_html' => $summaryHtml,
            'count' => $data['preferences']->count(),
        ]);
    }

    protected function buildData(?string $query = null): array
    {
        $preferencesQuery = GuestPreference::query()
            ->with([
                'guest' => fn ($guestQuery) => $guestQuery->withTrashed()->with(['table' => fn ($tableQuery) => $tableQuery->withTrashed()]),
                'beverage',
            ]);

        if ($query !== null && $query !== '') {
            $preferencesQuery->where(function ($subQuery) use ($query) {
                $subQuery->whereHas('guest', function ($guestQuery) use ($query) {
                    $guestQuery->where('primary_first_name', 'like', "%{$query}%")
                        ->orWhere('secondary_first_name', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhereHas('table', function ($tableQuery) use ($query) {
                            $tableQuery->where('name', 'like', "%{$query}%");
                        });
                })->orWhereHas('beverage', function ($beverageQuery) use ($query) {
                    $beverageQuery->where('name', 'like', "%{$query}%");
                })->orWhere('notes', 'like', "%{$query}%");
            });
        }

        $preferences = $preferencesQuery->orderByDesc('created_at')->get();

        $summary = $preferences
            ->groupBy('beverage_id')
            ->map(function ($items) {
                $beverage = $items->first()->beverage;

                return [
                    'beverage' => $beverage,
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        return [
            'preferences' => $preferences,
            'summary' => $summary,
        ];
    }

    /**
     * Exporte les statistiques des préférences en PDF.
     */
    public function export()
    {
        $filename = 'statistiques_preferences_'.now()->format('Y-m-d_His').'.pdf';

        // Récupérer toutes les préférences avec les boissons
        $preferences = GuestPreference::with('beverage')
            ->get();

        // Grouper par catégorie puis par boisson
        $statsByCategory = $preferences
            ->groupBy(function ($preference) {
                return $preference->beverage->category ?? 'autre';
            })
            ->map(function ($categoryPreferences, $category) {
                // Grouper par boisson dans cette catégorie
                $beverageStats = $categoryPreferences
                    ->groupBy('beverage_id')
                    ->map(function ($items, $beverageId) {
                        $beverage = $items->first()->beverage;
                        return [
                            'name' => $beverage->name,
                            'count' => $items->count(),
                        ];
                    })
                    ->sortByDesc('count')
                    ->values();

                return [
                    'category' => ucfirst($category),
                    'beverages' => $beverageStats,
                    'total' => $categoryPreferences->count(),
                ];
            })
            ->sortKeys();

        $pdf = Pdf::loadView('preferences.export-pdf', [
            'statsByCategory' => $statsByCategory,
            'title' => 'Statistiques des préférences',
        ]);

        return $pdf->download($filename);
    }
}
