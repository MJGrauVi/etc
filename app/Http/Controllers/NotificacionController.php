<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * 1. Listar TODAS (Historial: leídas y no leídas)
     */
    public function index()
    {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'data' => $user->notifications, // Trae todas
        ]);
    }

    /**
     * 2. Obtener SOLO las no leídas (Para la campana)
     */
    public function unread()
    {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'data' => $user->unreadNotifications,
            'count' => $user->unreadNotifications->count()
        ]);
    }

    /**
     * Marcar una específica como leída
     */
    public function markAsRead($id)
    {
        $notificacion = Auth::user()->notifications()->findOrFail($id);
        $notificacion->markAsRead();

        return response()->json(['message' => 'Notificación marcada como leída']);
    }

    /**
     * Marcar TODAS como leídas
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas']);
    }
}
