<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Obtenir le nombre de notifications non lues
     */
    public function count(): JsonResponse
    {
        $count = Auth::user()->unreadNotificationsCount();

        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * Obtenir la liste des notifications
     */
    public function index(): JsonResponse
    {
        $notifications = Auth::user()
            ->notifications()
            ->with('guest.table')
            ->take(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        // Vérifier que la notification appartient à l'utilisateur connecté
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue',
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(): JsonResponse
    {
        Auth::user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues',
        ]);
    }
}
