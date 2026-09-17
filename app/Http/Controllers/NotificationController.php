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
     * Marquer une notification comme lue et rediriger gracieusement.
     */
    public function markAsRead(string $id)
    {
        $user = $this->getEffectiveUser();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('dashboard');

        // F-14 : Sécuriser contre les redirections ouvertes (Open Redirect)
        $appUrl = rtrim(config('app.url') ?? '', '/');
        $isInternal = false;

        if (is_string($url) && $url !== '') {
            if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
                $isInternal = true;
            } elseif (!empty($appUrl) && str_starts_with($url, $appUrl)) {
                $isInternal = true;
            }
        }

        if (!$isInternal) {
            $url = route('dashboard');
        }

        return $this->resolveNotificationRedirect($url, $user);
    }

    /**
     * Résout gracieusement la destination d'une notification si la ressource ciblée est introuvable ou inaccessible.
     */
    private function resolveNotificationRedirect(string $url, User $user)
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '/';

        // 1. Cible Activité (ex: /activities/XXX ou /admin/activities/XXX)
        if (preg_match('#(?:admin/)?activities/([A-Za-z0-9_-]+)#', $path, $matches)) {
            $identifier = $matches[1];

            // Ne pas intercepter les sous-routes fonctionnelles
            if (!in_array($identifier, ['create', 'attendance', 'scan', 'success', 'download-registrations', 'download-attendance'])) {
                $activityId = decode_id($identifier) ?? (is_numeric($identifier) ? (int) $identifier : null);
                
                $activity = $activityId ? \App\Models\Activity::find($activityId) : null;

                // Si l'activité n'existe pas ou appartient à une autre église
                if (!$activity) {
                    return redirect()->route('activities.index')->with('info', 'L\'activité liée à cette notification n\'est plus disponible ou a été supprimée.');
                }

                // Si l'activité n'est plus publiée
                if ($activity->status !== \App\Enums\ActivityStatus::PUBLISHED) {
                    $canPreview = $user->hasRole('Administrateur') ||
                        $user->isSuperAdmin() ||
                        $user->can(\App\Enums\PermissionEnum::ATTENDANCE_VALIDATE_MANUAL_ALL->value) ||
                        $activity->responsible_id === $user->id;

                    if (!$canPreview) {
                        return redirect()->route('activities.index')->with('warning', 'Cette activité n\'est plus disponible ou a été annulée.');
                    }
                }

                // Si le lien pointait vers /admin/activities/... mais que l'utilisateur est un jeune sans accès admin
                if (str_contains($path, '/admin/activities/')) {
                    if (!$user->hasRole('Administrateur') && !$user->can(\App\Enums\PermissionEnum::ACTIVITY_VIEW->value)) {
                        return redirect()->route('activities.show', $activity);
                    }
                }
            }
        }

        // 2. Cible Groupe (ex: /admin/groups/XXX)
        if (preg_match('#admin/groups/([A-Za-z0-9_-]+)#', $path, $matches)) {
            $identifier = $matches[1];
            if (!in_array($identifier, ['create'])) {
                $groupId = decode_id($identifier) ?? (is_numeric($identifier) ? (int) $identifier : null);
                $group = $groupId ? \App\Models\Group::find($groupId) : null;

                if (!$group) {
                    return redirect()->route('dashboard')->with('info', 'Le groupe lié à cette notification n\'est plus disponible.');
                }

                // Si l'utilisateur n'a pas accès à la gestion globale des groupes
                if (!$user->can('access-group-management')) {
                    return redirect()->route('profile.edit')->with('info', 'Vous faites partie du groupe : ' . $group->name);
                }
            }
        }

        // 3. Cible Finance Admin (ex: /admin/finance/...)
        if (str_contains($path, '/admin/finance/')) {
            if (!$user->can(\App\Enums\PermissionEnum::FINANCE_COLLECT_OWN_GROUP->value) &&
                !$user->can(\App\Enums\PermissionEnum::REMITTANCE_VIEW_ALL->value) &&
                !$user->hasRole('Administrateur')) {
                return redirect()->route('profile.edit')->with('info', 'Votre situation financière a été enregistrée.');
            }
        }

        // 4. Cible Utilisateur / Membre (ex: /admin/users/XXX)
        if (preg_match('#admin/users/([A-Za-z0-9_-]+)#', $path, $matches)) {
            $identifier = $matches[1];
            if (!in_array($identifier, ['create', 'export', 'bulk-status'])) {
                $targetUserId = decode_id($identifier) ?? (is_numeric($identifier) ? (int) $identifier : null);
                $targetUser = $targetUserId ? User::find($targetUserId) : null;

                if (!$targetUser) {
                    return redirect()->route('admin.users.index')->with('info', 'Le compte de ce membre n\'est plus disponible ou a été supprimé.');
                }

                if (!$user->can('manage-users')) {
                    return redirect()->route('dashboard')->with('info', 'Notification consultée.');
                }
            }
        }

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
