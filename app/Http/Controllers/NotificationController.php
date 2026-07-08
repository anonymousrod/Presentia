<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('dashboard');

        return redirect($url);
    }

    /**
     * Afficher toutes les notifications.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Supprimer une notification.
     */
    public function destroy(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification supprimée avec succès.');
    }

    /**
     * Supprimer toutes les notifications.
     */
    public function destroyAll()
    {
        auth()->user()->notifications()->delete();

        return back()->with('success', 'Toutes les notifications ont été supprimées.');
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
