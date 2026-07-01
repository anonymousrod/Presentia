<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
