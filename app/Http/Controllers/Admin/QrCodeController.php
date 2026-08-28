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
        $this->authorize('qrcode.generate');

        $expires = $activity->end_time;

        $url = URL::temporarySignedRoute(
            'attendance.validate',
            $expires,
            ['activity' => encode_id($activity->id), 'v' => $activity->qr_version]
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
        $this->authorize('qrcode.revoke');

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
        $this->authorize('qrcode.generate');

        $url = session("activity_qr_url_{$activity->id}");

        if (!$url) {
            $expires = $activity->end_time;
            $url = URL::temporarySignedRoute(
                'attendance.validate',
                $expires,
                ['activity' => encode_id($activity->id), 'v' => $activity->qr_version]
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

        $church = $activity->church ?? (session('tenant_church_id') ? \App\Models\Church::find(session('tenant_church_id')) : auth()->user()?->church) ?? \App\Models\Church::first();

        $settings = $church ? \App\Models\AppSetting::where('church_id', $church->id)->first() : null;
        if (!$settings) {
            $settings = \App\Models\AppSetting::find(1);
        }

        $logo1Path = $settings?->pdf_logo_1 ?: ($church?->logo_path ?: ($settings?->logo_dark ?: 'assets/images/Icone J-EBER.png'));
        $logoUeebBase64 = $this->getLogoBase64($logo1Path);

        $pdf = Pdf::loadView('admin.activities.qr-pdf', compact('activity', 'qrCodeDataUri', 'logoUeebBase64', 'church'));
        return $pdf->download("QR_Code_{$activity->id}_{$activity->title}.pdf");
    }

    private function getLogoBase64(?string $path): string
    {
        if (!$path) {
            return '';
        }

        $fullPath = null;
        if (file_exists(public_path($path))) {
            $fullPath = public_path($path);
        } elseif (file_exists(public_path('storage/' . $path))) {
            $fullPath = public_path('storage/' . $path);
        } elseif (file_exists(storage_path('app/public/' . $path))) {
            $fullPath = storage_path('app/public/' . $path);
        } elseif (file_exists(public_path('assets/images/' . basename($path)))) {
            $fullPath = public_path('assets/images/' . basename($path));
        }

        if ($fullPath && file_exists($fullPath)) {
            $mime = @mime_content_type($fullPath) ?: 'image/png';
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
        }

        return '';
    }
}
