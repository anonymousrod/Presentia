<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeController extends Controller
{
    /**
     * Génère l'URL signée temporaire pour l'activité.
     */
    public function generate(Activity $activity)
    {
        $expires = $activity->end_time;

        $url = URL::temporarySignedRoute(
            'attendance.validate',
            $expires,
            ['activity' => $activity->id, 'v' => $activity->qr_version]
        );

        session()->put("activity_qr_url_{$activity->id}", $url);
        session()->put("activity_qr_expires_{$activity->id}", $expires->timestamp);

        return redirect()->route('admin.activities.show', $activity)
            ->with('success', 'QR Code de présence généré avec succès.');
    }

    /**
     * Révoque le QR code actuel en incrémentant la version.
     */
    public function revoke(Activity $activity)
    {
        $activity->increment('qr_version');

        session()->forget("activity_qr_url_{$activity->id}");
        session()->forget("activity_qr_expires_{$activity->id}");

        return redirect()->route('admin.activities.show', $activity)
            ->with('success', 'QR Code révoqué avec succès. Toutes les signatures précédentes sont désormais invalides.');
    }

    /**
     * Télécharge le QR Code en format PDF pour l'impression.
     */
    public function downloadPdf(Activity $activity)
    {
        $url = session("activity_qr_url_{$activity->id}");

        if (!$url) {
            $expires = $activity->end_time;
            $url = URL::temporarySignedRoute(
                'attendance.validate',
                $expires,
                ['activity' => $activity->id, 'v' => $activity->qr_version]
            );

            session()->put("activity_qr_url_{$activity->id}", $url);
            session()->put("activity_qr_expires_{$activity->id}", $expires->timestamp);
        }

        // Utilisation de QrCode et PngWriter pour assurer la compatibilité maximale dans DomPDF
        $qrCode = new \Endroid\QrCode\QrCode(
            data: $url,
            size: 300,
            margin: 10
        );
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);

        $qrCodeDataUri = $result->getDataUri();

        $pdf = Pdf::loadView('admin.activities.qr-pdf', compact('activity', 'qrCodeDataUri'));


        return $pdf->download("QR_Code_{$activity->id}_{$activity->title}.pdf");
    }
}
