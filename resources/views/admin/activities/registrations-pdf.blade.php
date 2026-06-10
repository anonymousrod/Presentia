<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Liste d'inscriptions — {{ $activity->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            background: #ffffff;
            line-height: 1.4;
        }

        /* ═══════════════════════════════════════
           BANDE DÉCORATIVE HAUT
        ═══════════════════════════════════════ */
        .top-bar {
            background-color: #1a1a2e;
            height: 6px;
            width: 100%;
        }

        /* ═══════════════════════════════════════
           EN-TÊTE
        ═══════════════════════════════════════ */
        .header-wrapper {
            padding: 10mm 15mm 6mm 15mm;
            background: #ffffff;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-logo-left {
            width: 22%;
            text-align: left;
            vertical-align: middle;
        }

        .header-logo-left img {
            width: 110px;
            height: auto;
        }

        .header-title-center {
            width: 56%;
            text-align: center;
            vertical-align: middle;
            padding: 0 5mm;
        }

        .org-name {
            font-size: 12px;
            font-weight: bold;
            color: #1a1a2e;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .dept-name {
            font-size: 10px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-logo-right {
            width: 22%;
            text-align: right;
            vertical-align: middle;
        }

        .header-logo-right img {
            width: 120px;
            height: auto;
        }

        /* ═══════════════════════════════════════
           SÉPARATEUR
        ═══════════════════════════════════════ */
        .divider {
            margin: 0 15mm;
            border: none;
            border-top: 2px solid #1a1a2e;
        }

        .divider-thin {
            margin: 0 15mm;
            border: none;
            border-top: 1px solid #dde3ea;
        }

        /* ═══════════════════════════════════════
           BLOC TITRE DU DOCUMENT
        ═══════════════════════════════════════ */
        .title-block {
            padding: 6mm 15mm 5mm 15mm;
            text-align: center;
        }

        .doc-type-label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 3px;
        }

        .doc-main-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a2e;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .activity-name-title {
            font-size: 11px;
            color: #c0392b;
            font-weight: bold;
            margin-top: 3px;
            text-transform: uppercase;
        }

        /* ═══════════════════════════════════════
           FICHE INFO ACTIVITÉ
        ═══════════════════════════════════════ */
        .meta-section {
            padding: 4mm 15mm;
        }

        .meta-box {
            width: 100%;
            border-collapse: collapse;
            background: #f4f6fa;
            border-left: 4px solid #1a1a2e;
        }

        .meta-box td {
            padding: 4px 8px;
            font-size: 9.5px;
            color: #333;
            vertical-align: top;
        }

        .meta-box td span.label {
            font-weight: bold;
            color: #1a1a2e;
            text-transform: uppercase;
            font-size: 8.5px;
            display: block;
            margin-bottom: 1px;
        }

        /* ═══════════════════════════════════════
           SECTION TABLEAU
        ═══════════════════════════════════════ */
        .table-section {
            padding: 4mm 15mm 0 15mm;
        }

        .section-heading {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            margin-bottom: 4px;
            border-bottom: 1px solid #dde3ea;
            padding-bottom: 3px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        .data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table thead tr th {
            background-color: #1a1a2e;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 6px;
            text-align: center;
            border: 1px solid #1a1a2e;
        }

        .data-table tbody tr:nth-child(even) td {
            background-color: #f4f6fa;
        }

        .data-table tbody tr:nth-child(odd) td {
            background-color: #ffffff;
        }

        .data-table tbody td {
            border: 1px solid #dde3ea;
            padding: 6px 6px;
            font-size: 9px;
            color: #2c2c2c;
            vertical-align: middle;
        }

        .data-table td.center {
            text-align: center;
        }

        .badge-ok {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-justified {
            background-color: #fff3e0;
            color: #e65100;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        /* ═══════════════════════════════════════
           PIED DE PAGE
        ═══════════════════════════════════════ */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 14mm;
            background: #f4f6fa;
            border-top: 2px solid #1a1a2e;
            padding: 3mm 15mm;
        }

        .footer-inner {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            width: 70%;
            vertical-align: middle;
            font-size: 7.5px;
            color: #555;
        }

        .footer-left .cert-text {
            font-style: italic;
        }

        .footer-left .cert-text strong {
            color: #1a1a2e;
            font-style: normal;
        }

        .footer-right {
            width: 30%;
            text-align: right;
            vertical-align: middle;
            font-size: 7.5px;
            color: #888;
        }

        /* Espace pour le pied de page fixe */
        .footer-spacer {
            height: 18mm;
        }
    </style>
</head>
<body>

    <!-- BANDE DÉCORATIVE -->
    <div class="top-bar"></div>

    <!-- EN-TÊTE -->
    <div class="header-wrapper">
        <table class="header-table">
            <tr>
                <td class="header-logo-left">
                    @if($logoUeebBase64)
                        <img src="{{ $logoUeebBase64 }}" alt="UEEB">
                    @endif
                </td>
                <td class="header-title-center">
                    <div class="org-name">Eglise Baptiste de l'Etoile Rouge</div>
                    <div class="dept-name">Département de la Jeunesse</div>
                </td>
                <td class="header-logo-right">
                    @if($logoJeunesseBase64)
                        <img src="{{ $logoJeunesseBase64 }}" alt="Jeunesse Etoile Rouge">
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <hr class="divider">

    <!-- TITRE DU DOCUMENT -->
    <div class="title-block">
        <div class="doc-type-label">Document Officiel</div>
        <div class="doc-main-title">Liste des Inscriptions</div>
        <div class="activity-name-title">{{ $activity->title }}</div>
    </div>

    <hr class="divider-thin">

    <!-- MÉTADONNÉES ACTIVITÉ -->
    <div class="meta-section">
        <table class="meta-box">
            <tr>
                <td style="width:25%;">
                    <span class="label">Date</span>
                    {{ $activity->start_time->format('d/m/Y') }}
                </td>
                <td style="width:25%;">
                    <span class="label">Lieu</span>
                    {{ $activity->location ?: 'EB Étoile Rouge' }}
                </td>
                <td style="width:25%;">
                    <span class="label">Responsable</span>
                    {{ $activity->responsible ? $activity->responsible->full_name : '—' }}
                </td>
                <td style="width:25%;">
                    <span class="label">Total inscrits</span>
                    {{ $registrations->count() }} participant(s)
                </td>
            </tr>
            @if($activity->description)
            <tr>
                <td colspan="4">
                    <span class="label">Thème / Description</span>
                    {{ Str::limit($activity->description, 120) }}
                </td>
            </tr>
            @endif
        </table>
    </div>

    <!-- TABLEAU DES INSCRIPTIONS -->
    <div class="table-section">
        <div class="section-heading">Participants inscrits</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:5%;">N</th>
                    <th style="width:27%;">Nom Complet</th>
                    <th style="width:28%;">Email</th>
                    <th style="width:17%;">Date d'inscription</th>
                    <th style="width:23%;">Motif / Justification</th>
                </tr>
            </thead>
            <tbody>
                @php $rowNum = 1; @endphp
                @foreach($registrations as $reg)
                    <tr>
                        <td class="center">{{ $rowNum++ }}</td>
                        <td>{{ $reg->user->full_name }}</td>
                        <td>{{ $reg->user->email ?? '—' }}</td>
                        <td class="center">{{ $reg->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($reg->status->value === 'ABSENT_JUSTIFIED')
                                <span class="badge-justified">{{ $reg->justification ?: 'Aucun motif renseigné' }}</span>
                            @else
                                <span class="badge-ok">Inscrit(e)</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer-spacer"></div>

    <!-- PIED DE PAGE -->
    <div class="footer">
        <table class="footer-inner">
            <tr>
                <td class="footer-left">
                    <span class="cert-text">Ce document officiel a été généré électroniquement et certifié conforme par la plateforme de gestion <strong>Presentia</strong>.</span>
                </td>
                <td class="footer-right">
                    Généré le {{ now()->format('d/m/Y à H:i') }}<br>
                    Liste d'inscriptions — {{ $activity->title }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
