<?php

namespace App\Http\Controllers;

use App\Models\User;

class NotificationController extends Controller
{
    /**
     * Obtenir l'utilisateur effectif (l'administrateur de l'église cliente en mode support, sinon l'utilisateur authentifié).
     */
    private function getEffectiveUser(): User
    {
        $user = auth()->user();

        if (session()->has('tenant_church_id') && $user && $user->isSuperAdmin()) {
            $churchId = session('tenant_church_id');
            $churchAdmin = User::withoutGlobalScopes()
                ->where('church_id', $churchId)
                ->whereHas('roles', fn ($q) => $q->where('name', 'Administrateur'))
                ->first() ?? User::withoutGlobalScopes()->where('church_id', $churchId)->first();

            if ($churchAdmin) {
                return $churchAdmin;
            }
        }

        return $user;
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead(string $id)
    {
        $user = $this->getEffectiveUser();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('dashboard');

        return redirect($url);
    }

    /**
     * Afficher toutes les notifications.
     */
    public function index()
    {
        $user = $this->getEffectiveUser();
        $notifications = $user->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Supprimer une notification.
     */
    public function destroy(string $id)
    {
        $user = $this->getEffectiveUser();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification supprimée avec succès.');
    }

    /**
     * Supprimer toutes les notifications.
     */
    public function destroyAll()
    {
        $user = $this->getEffectiveUser();
        $user->notifications()->delete();

        return back()->with('success', 'Toutes les notifications ont été supprimées.');
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead()
    {
        $user = $this->getEffectiveUser();
        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
