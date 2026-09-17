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
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        /* ============ PAGE WRAPPER ============ */
        .page-wrapper {
            width: 100%;
            background: #ffffff;
            position: relative;
        }

        /* ============ HEADER ============ */
        .header-block {
            width: 100%;
            background-color: #4338CA;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .header-logo-cell {
            width: 62%;
            vertical-align: middle;
            padding: 20px 10px 18px 30px;
            text-align: left;
        }

        .header-badge-cell {
            width: 38%;
            vertical-align: middle;
            text-align: right;
            padding: 20px 30px 18px 10px;
        }

        .badge-type {
            display: inline-block;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.35);
            color: #ffffff;
            font-size: 9.5px;
            font-weight: bold;
            padding: 6px 14px;
            border-radius: 20px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .badge-version {
            font-size: 9px;
            color: #c7d2fe;
            margin-top: 6px;
            text-align: right;
            white-space: nowrap;
        }

        /* ============ ACCENT STRIPE ============ */
        .accent-stripe {
            width: 100%;
            height: 4px;
            background-color: #6366f1;
        }

        /* ============ MAIN CONTENT ============ */
        .content-area {
            padding: 26px 30px 20px 30px;
        }

        /* ============ ACTIVITY TITLE BLOCK ============ */
        .title-block {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 18px;
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
            font-size: 20px;
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
            background-color: #4338CA;
            border-radius: 16px;
        }

        .qr-inner-box {
            background: #ffffff;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
        }

        .qr-image {
            width: 200px;
            height: 200px;
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

        /* ============ STEPS BOX ============ */
        .steps-wrapper {
            margin-top: 18px;
        }

        .steps-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .step-cell {
            vertical-align: top;
        }

        .step-card {
            border-radius: 8px;
            padding: 10px 11px;
            min-height: 95px;
        }

        .step-badge {
            display: inline-block;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            padding: 2.5px 7px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .step-title {
            font-size: 10.5px;
            font-weight: bold;
            margin-top: 6px;
            margin-bottom: 4px;
        }

        .step-desc {
            font-size: 8.5px;
            line-height: 1.35;
        }

        /* ============ SEPARATOR ============ */
        .separator {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 16px 0;
        }

        /* ============ FOOTER ============ */
        .footer-band {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            background-color: #4338CA;
            border-top: 3px solid #6366f1;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .footer-left {
            width: 75%;
            vertical-align: middle;
            padding: 13px 10px 13px 30px;
        }

        .footer-right {
            width: 25%;
            vertical-align: middle;
            text-align: right;
            padding: 13px 30px 13px 10px;
        }

        .footer-security {
            font-size: 8.5px;
            color: #ffffff;
            font-weight: bold;
            margin-bottom: 2px;
            word-wrap: break-word;
        }

        .footer-meta {
            font-size: 8.5px;
            color: #c7d2fe;
            word-wrap: break-word;
        }

        .watermark-badge {
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 12px;
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
                    <table style="border: none; border-collapse: collapse; padding: 0; margin: 0;">
                        <tr>
                            @if(!empty($logoUeebBase64))
                            <td style="padding: 0 12px 0 0; vertical-align: middle; line-height: 1;">
                                <img src="{{ $logoUeebBase64 }}" alt="Logo" height="34" style="vertical-align: middle; max-width: 38px;">
                            </td>
                            @else
                            <td style="padding: 0 10px 0 0; vertical-align: middle; line-height: 1; font-size: 24px; color: #ffffff;">
                                &#9670;
                            </td>
                            @endif
                            <td style="padding: 0; vertical-align: middle; line-height: 1.2;">
                                <div style="font-size: 15px; font-weight: bold; color: #ffffff; letter-spacing: 0.5px; text-transform: uppercase; word-wrap: break-word;">
                                    {{ $church->name ?? ($activity->church->name ?? config('app.name')) }}
                                </div>
                                <div style="font-size: 8.5px; color: #c7d2fe; letter-spacing: 0.5px; text-transform: uppercase; margin-top: 3px;">
                                    {{ ($church->city ?? $activity->church?->city) ? ($church->city ?? $activity->church?->city) . ' — ' : '' }}Système de Gestion des Présences
                                </div>
                            </td>
                        </tr>
                    </table>
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

        <!-- ÉTAPES DE POINTAGE EXPRESS -->
        <div class="steps-wrapper">
            <div class="section-heading">&#9632; Comment marquer votre présence ? (En 3 étapes)</div>
            <table class="steps-table">
                <tr>
                    <td class="step-cell" style="width: 33.33%; padding-right: 6px;">
                        <div class="step-card" style="background: #f5f3ff; border: 1px solid #ddd6fe; border-top: 3px solid #6366f1;">
                            <span class="step-badge" style="background: #4f46e5;">Étape 1</span>
                            <div class="step-title" style="color: #312e81;">Enrôlement (1 fois)</div>
                            <div class="step-desc" style="color: #4338ca;">Cliquez sur le bouton ci-dessus pour faire de ce navigateur votre badge sécurisé.</div>
                        </div>
                    </td>
                    <td class="step-cell" style="width: 33.33%; padding-left: 3px; padding-right: 3px;">
                        <div class="step-card" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-top: 3px solid #10b981;">
                            <span class="step-badge" style="background: #059669;">Étape 2</span>
                            <div class="step-title" style="color: #065f46;">Scan à l'église</div>
                            <div class="step-desc" style="color: #047857;">Ouvrez simplement l'appareil photo ou le scanner de votre smartphone et visez le QR Code de l'activité.</div>
                        </div>
                    </td>
                    <td class="step-cell" style="width: 33.33%; padding-left: 6px;">
                        <div class="step-card" style="background: #f0f9ff; border: 1px solid #bae6fd; border-top: 3px solid #0284c7;">
                            <span class="step-badge" style="background: #0284c7;">Étape 3</span>
                            <div class="step-title" style="color: #075985;">Validation Express</div>
                            <div class="step-desc" style="color: #0369a1;">Le système vous identifie automatiquement et affiche immédiatement l'écran vert de confirmation !</div>
                        </div>
                    </td>
                </tr>
            </table>
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