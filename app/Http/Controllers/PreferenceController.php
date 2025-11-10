<?php

namespace App\Http\Controllers;

use App\Models\Beverage;
use App\Models\Guest;
use App\Models\GuestPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreferenceController extends Controller
{
    public function index(): View
    {
        $data = $this->buildData();

        return view('preferences.index', $data);
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
}
