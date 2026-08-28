<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche d'Émargement — {{ $activity->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1a202c;
            background: #ffffff;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        /* ============ PAGE WRAPPER ============ */
        .page-wrapper {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
        }

        /* ============ HEADER ============ */
        .header-block {
            width: 100%;
            background: #4338CA;
            padding: 24px 48px 22px 40px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-logo-cell {
            width: 55%;
            vertical-align: middle;
        }

        .header-badge-cell {
            width: 45%;
            vertical-align: middle;
            text-align: right;
            padding-right: 4px;
        }

        .org-name {
            font-size: 26px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .org-tagline {
            font-size: 10px;
            color: #c7d2fe;
            margin-top: 4px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .badge-type {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.35);
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            padding: 6px 18px;
            border-radius: 20px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .badge-version {
            font-size: 9.5px;
            color: #a5b4fc;
            margin-top: 7px;
            text-align: right;
        }

        /* ============ ACCENT STRIPE ============ */
        .accent-stripe {
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #6366f1, #8b5cf6, #ec4899);
        }

        /* ============ MAIN CONTENT ============ */
        .content-area {
            padding: 28px 40px 24px 40px;
        }

        /* ============ ACTIVITY TITLE BLOCK ============ */
        .title-block {
            text-align: center;
            margin-bottom: 26px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .activity-label {
            font-size: 9.5px;
            color: #6366f1;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .activity-title {
            font-size: 21px;
            font-weight: bold;
            color: #1e1b4b;
        }

        /* ============ SECTION HEADING ============ */
        .section-heading {
            font-size: 9px;
            font-weight: bold;
            color: #6366f1;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e0e7ff;
        }

        /* ============ INFO TABLE ============ */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 8px 12px;
            font-size: 11px;
            vertical-align: middle;
        }

        .info-table tr:nth-child(odd) td {
            background: #f8faff;
        }

        .info-table tr:nth-child(even) td {
            background: #ffffff;
        }

        .info-label {
            width: 40%;
            color: #4a5568;
            font-weight: bold;
            border-left: 3px solid #6366f1;
        }

        .info-value {
            color: #1a202c;
        }

        /* ============ QR CODE ============ */
        .qr-outer-border {
            display: inline-block;
            padding: 3px;
            background: linear-gradient(135deg, #4338CA, #7c3aed);
            border-radius: 16px;
        }

        .qr-inner-box {
            background: #ffffff;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
        }

        .qr-image {
            width: 210px;
            height: 210px;
            display: block;
            margin: 0 auto;
        }

        .qr-caption {
            font-size: 9.5px;
            color: #6b7280;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: bold;
            text-align: center;
        }

        /* ============ VALIDITY BOX ============ */
        .validity-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 14px;
            text-align: center;
        }

        .validity-label {
            font-size: 9.5px;
            color: #15803d;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .validity-time {
            font-size: 11px;
            color: #166534;
            margin-top: 4px;
            font-weight: bold;
        }

        /* ============ INSTRUCTIONS ============ */
        .instructions-box {
            background: #eff6ff;
            border-left: 4px solid #6366f1;
            border-radius: 0 8px 8px 0;
            padding: 13px 18px;
            margin-top: 22px;
        }

        .instructions-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #3730a3;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 9px;
        }

        .instructions-line {
            font-size: 10.5px;
            color: #1e40af;
            padding: 2.5px 0;
            padding-left: 14px;
        }

        .bullet {
            color: #6366f1;
            font-weight: bold;
        }

        /* ============ SEPARATOR ============ */
        .separator {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 16px 0;
        }

        /* ============ FOOTER ============ */
        .footer-band {
            background: #1e1b4b;
            padding: 13px 48px 13px 40px;
            margin-top: 30px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            width: 70%;
            vertical-align: middle;
        }

        .footer-right {
            width: 30%;
            vertical-align: middle;
            text-align: right;
        }

        .footer-security {
            font-size: 9px;
            color: #818cf8;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .footer-meta {
            font-size: 9px;
            color: #6366f1;
        }

        .watermark-badge {
            background: #312e81;
            color: #818cf8;
            font-size: 8px;
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 4px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<div class="page-wrapper">

    <!-- ===== HEADER ===== -->
    <div class="header-block">
        <table class="header-table">
            <tr>
                <td class="header-logo-cell">
                    <div class="org-name">
                        <table style="border: none; border-collapse: collapse; padding: 0; margin: 0;">
                            <tr>
                                @if(!empty($logoUeebBase64))
                                <td style="padding: 0 10px 0 0; vertical-align: middle; line-height: 1;">
                                    <img src="{{ $logoUeebBase64 }}" alt="Logo" height="32" style="vertical-align: middle;">
                                </td>
                                @else
                                <td style="padding: 0 10px 0 0; vertical-align: middle; line-height: 1; font-size: 24px; color: #ffffff;">
                                    &#9670;
                                </td>
                                @endif
                                <td style="padding: 0; vertical-align: middle; line-height: 1.1;">
                                    <div style="font-size: 17px; font-weight: bold; color: #ffffff; letter-spacing: 1px; text-transform: uppercase;">
                                        {{ $church->name ?? ($activity->church->name ?? config('app.name')) }}
                                    </div>
                                    <div style="font-size: 9px; color: #c7d2fe; letter-spacing: 0.5px; text-transform: uppercase; margin-top: 2px;">
                                        {{ ($church->city ?? $activity->church?->city) ? ($church->city ?? $activity->church?->city) . ' — ' : '' }}Système de Gestion des Présences
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td class="header-badge-cell">
                    <div class="badge-type">&#10003; Fiche d'Émargement</div>
                    <div class="badge-version">QR Session — {{ $activity->start_time->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ===== ACCENT STRIPE ===== -->
    <div class="accent-stripe"></div>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="content-area">

        <!-- TITRE ACTIVITÉ -->
        <div class="title-block">
            <div class="activity-label">&#9654; Activité concernée</div>
            <div class="activity-title">{{ $activity->title }}</div>
        </div>

        <!-- COLONNES : INFO + QR -->
        <table style="width:100%; border-collapse:collapse;">
            <tr>

                <!-- COLONNE GAUCHE : INFOS -->
                <td style="width:54%; vertical-align:top; padding-right:20px;">

                    <div class="section-heading">&#9632; Informations de l'activité</div>

                    <table class="info-table">
                        <tr>
                            <td class="info-label">&#128197; Date</td>
                            <td class="info-value">{{ $activity->start_time->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">&#9200; Horaires</td>
                            <td class="info-value">
                                De {{ $activity->start_time->format('H:i') }}
                                à {{ $activity->end_time->format('H:i') }}
                            </td>
                        </tr>
                        @if($activity->location)
                        <tr>
                            <td class="info-label">&#128205; Lieu</td>
                            <td class="info-value">{{ $activity->location }}</td>
                        </tr>
                        @endif
                        @if($activity->responsible)
                        <tr>
                            <td class="info-label">&#128100; Responsable</td>
                            <td class="info-value">{{ $activity->responsible->first_name }} {{ $activity->responsible->name }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="info-label">&#127919; Type</td>
                            <td class="info-value">{{ $activity->activityType?->name ?? 'N/A' }}</td>
                        </tr>
                        @if($activity->capacity)
                        <tr>
                            <td class="info-label">&#128101; Capacité</td>
                            <td class="info-value">{{ $activity->capacity }} participants max.</td>
                        </tr>
                        @endif
                    </table>

                    <hr class="separator">

                    <!-- VALIDITÉ -->
                    <div class="validity-box">
                        <div class="validity-label">&#8987; Validité du QR Code</div>
                        <div class="validity-time">
                            Jusqu'au {{ $activity->end_time->format('d/m/Y') }}
                            à {{ $activity->end_time->format('H:i') }}
                        </div>
                    </div>

                </td>

                <!-- COLONNE DROITE : QR CODE -->
                <td style="width:46%; vertical-align:top; text-align:center; padding-left:4px;">

                    <div class="section-heading" style="text-align:center;">&#9632; QR Code de présence</div>

                    <div class="qr-outer-border">
                        <div class="qr-inner-box">
                            <img src="{{ $qrCodeDataUri }}" class="qr-image" alt="QR Code de présence">
                        </div>
                    </div>

                    <div class="qr-caption">Scanner pour émarger</div>

                </td>
            </tr>
        </table>

        <!-- INSTRUCTIONS -->
        <div class="instructions-box">
            <div class="instructions-title">&#128161; Instructions de scan</div>
            <div class="instructions-line"><span class="bullet">&#9656;</span> Ouvrez l'application {{ config('app.name') }} sur votre smartphone et connectez-vous.</div>
            <div class="instructions-line"><span class="bullet">&#9656;</span> Pointez l'appareil photo vers ce QR Code pour enregistrer votre présence.</div>
            <div class="instructions-line"><span class="bullet">&#9656;</span> Un message de confirmation apparaîtra une fois votre présence validée.</div>
            <div class="instructions-line"><span class="bullet">&#9656;</span> Ce QR Code est personnel à cette session — toute copie ou modification est inutile.</div>
        </div>

    </div>

    <!-- ===== FOOTER ===== -->
    <div class="footer-band">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    <div class="footer-security">&#128274; Document officiel émis pour {{ $church->name ?? ($activity->church->name ?? config('app.name')) }} via {{ config('app.name') }}</div>
                    <div class="footer-meta">Généré le {{ now()->format('d/m/Y à H:i') }} — Session : {{ $activity->title }}</div>
                </td>
                <td class="footer-right">
                    <span class="watermark-badge">OFFICIEL</span>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>