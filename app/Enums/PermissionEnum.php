<?php

namespace App\Enums;

/**
 * Liste exhaustive de toutes les permissions de l'application Presentia.
 *
 * Convention : format 'resource.action'
 * Usage obligatoire : PermissionEnum::MEMBER_VIEW->value (jamais une chaîne brute)
 */
enum PermissionEnum: string
{
    // ----------------------------------------------------------------
    // Membres
    // ----------------------------------------------------------------
    case MEMBER_VIEW    = 'member.view';
    case MEMBER_CREATE  = 'member.create';
    case MEMBER_EDIT    = 'member.edit';
    case MEMBER_DELETE  = 'member.delete';
    case MEMBER_RESTORE = 'member.restore';
    case MEMBER_EXPORT  = 'member.export';

    // ----------------------------------------------------------------
    // Groupes
    // ----------------------------------------------------------------
    case GROUP_VIEW          = 'group.view';
    case GROUP_VIEW_OWN      = 'group.view_own';      // Chef : son groupe uniquement
    case GROUP_CREATE        = 'group.create';
    case GROUP_EDIT          = 'group.edit';
    case GROUP_DELETE        = 'group.delete';
    case GROUP_ASSIGN_MEMBER = 'group.assign_member';

    // ----------------------------------------------------------------
    // Activités
    // ----------------------------------------------------------------
    case ACTIVITY_VIEW    = 'activity.view';
    case ACTIVITY_CREATE  = 'activity.create';
    case ACTIVITY_EDIT    = 'activity.edit';

    // ----------------------------------------------------------------
    // Présences
    // ----------------------------------------------------------------
    case ATTENDANCE_VIEW             = 'attendance.view';
    case ATTENDANCE_VIEW_OWN         = 'attendance.view_own';  // Chef : son groupe uniquement
    case ATTENDANCE_VALIDATE_MANUAL_ALL  = 'attendance.validate_manual_all';
    case ATTENDANCE_VALIDATE_MANUAL_OWN  = 'attendance.validate_manual_own';
    case ATTENDANCE_SCAN_QR          = 'attendance.scan_qr';

    // ----------------------------------------------------------------
    // Inscriptions
    // ----------------------------------------------------------------
    case REGISTRATION_CREATE     = 'registration.create';
    case REGISTRATION_EDIT_OWN   = 'registration.edit_own';
    case REGISTRATION_CANCEL_OWN = 'registration.cancel_own';

    // ----------------------------------------------------------------
    // Notifications
    // ----------------------------------------------------------------
    case NOTIFICATION_SEND_ALL        = 'notification.send_all';
    case NOTIFICATION_SEND_GROUP      = 'notification.send_group';
    case NOTIFICATION_SEND_ROLE       = 'notification.send_role';
    case NOTIFICATION_SEND_INDIVIDUAL = 'notification.send_individual';

    // ----------------------------------------------------------------
    // Finances (Cotisations & Trésorerie)
    // ----------------------------------------------------------------
    case FINANCE_VIEW_ALL         = 'finance.view_all';
    case FINANCE_COLLECT_OWN_GROUP = 'finance.collect_own_group';
    case REMITTANCE_CREATE        = 'remittance.create';
    case REMITTANCE_VALIDATE      = 'remittance.validate';

    // ----------------------------------------------------------------
    // Statistiques & Rapports
    // ----------------------------------------------------------------
    case STATS_VIEW_GLOBAL        = 'stats.view_global';
    case STATS_VIEW_OWN_GROUP     = 'stats.view_own_group';

    // ----------------------------------------------------------------
    // Rôles & Permissions
    // ----------------------------------------------------------------
    case ROLE_MANAGE       = 'role.manage';
    case PERMISSION_MANAGE = 'permission.manage';

    // ----------------------------------------------------------------
    // Audit Trail
    // ----------------------------------------------------------------
    case AUDIT_VIEW = 'audit.view';

    // ----------------------------------------------------------------
    // QR Code
    // ----------------------------------------------------------------
    case QRCODE_GENERATE = 'qrcode.generate';
    case QRCODE_REVOKE   = 'qrcode.revoke';

    // ----------------------------------------------------------------
    // Méthodes utilitaires
    // ----------------------------------------------------------------

    /**
     * Retourne toutes les valeurs de permissions sous forme de tableau de chaînes.
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Retourne le libellé français lisible de chaque permission.
     */
    public function label(): string
    {
        return match ($this) {
            self::MEMBER_VIEW              => 'Voir les membres',
            self::MEMBER_CREATE            => 'Créer un membre',
            self::MEMBER_EDIT              => 'Modifier un membre',
            self::MEMBER_DELETE            => 'Supprimer un membre',
            self::MEMBER_RESTORE           => 'Restaurer un membre',
            self::MEMBER_EXPORT            => 'Exporter la liste des membres',
            self::GROUP_VIEW               => 'Voir tous les groupes',
            self::GROUP_VIEW_OWN           => 'Voir son propre groupe',
            self::GROUP_CREATE             => 'Créer un groupe',
            self::GROUP_EDIT               => 'Modifier un groupe',
            self::GROUP_DELETE             => 'Supprimer un groupe',
            self::GROUP_ASSIGN_MEMBER      => 'Assigner un membre à un groupe',
            self::ACTIVITY_VIEW            => 'Voir les activités',
            self::ACTIVITY_CREATE          => 'Créer une activité',
            self::ACTIVITY_EDIT            => 'Modifier une activité',
            self::ATTENDANCE_VIEW          => 'Voir toutes les présences',
            self::ATTENDANCE_VIEW_OWN      => 'Voir les présences de son groupe',
            self::ATTENDANCE_VALIDATE_MANUAL_ALL => 'Valider manuellement la présence de tout le monde',
            self::ATTENDANCE_VALIDATE_MANUAL_OWN => 'Valider manuellement la présence pour mon groupe',
            self::ATTENDANCE_SCAN_QR       => 'Scanner un QR code',
            self::REGISTRATION_CREATE      => 'S\'inscrire à une activité',
            self::REGISTRATION_EDIT_OWN    => 'Modifier sa propre inscription',
            self::REGISTRATION_CANCEL_OWN  => 'Annuler sa propre inscription',
            self::NOTIFICATION_SEND_ALL       => 'Envoyer une notification globale',
            self::NOTIFICATION_SEND_GROUP     => 'Envoyer une notification à un groupe',
            self::NOTIFICATION_SEND_ROLE      => 'Envoyer une notification à un rôle',
            self::NOTIFICATION_SEND_INDIVIDUAL => 'Envoyer une notification individuelle',
            self::FINANCE_VIEW_ALL         => 'Voir toutes les finances',
            self::FINANCE_COLLECT_OWN_GROUP => 'Collecter les fonds de son groupe',
            self::REMITTANCE_CREATE        => 'Déclarer un versement à la trésorerie',
            self::REMITTANCE_VALIDATE      => 'Valider un versement reçu',
            self::STATS_VIEW_GLOBAL        => 'Voir les statistiques globales',
            self::STATS_VIEW_OWN_GROUP     => 'Voir les statistiques de son groupe',
            self::ROLE_MANAGE              => 'Gérer les rôles',
            self::PERMISSION_MANAGE        => 'Gérer les permissions',
            self::AUDIT_VIEW               => 'Voir les journaux d\'audit',
            self::QRCODE_GENERATE          => 'Générer un QR code',
            self::QRCODE_REVOKE            => 'Révoquer un QR code',
        };
    }
}
