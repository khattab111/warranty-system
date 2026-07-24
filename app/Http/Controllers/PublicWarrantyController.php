<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PublicWarrantyController extends Controller
{
    public function show(string $publicToken): View
    {
        $warranty = Cache::remember("warranty.{$publicToken}", 60, function () use ($publicToken) {
            return Warranty::where('public_token', $publicToken)->first();
        });

        if (!$warranty) {
            abort(404, 'رمز الضمان غير صحيح أو غير موجود.');
        }

        $remaining = null;
        if ($warranty->activated_at !== null && !$warranty->is_expired) {
            $remaining = $warranty->remaining_days;
        }

        return view('public.warranty', [
            'warranty' => $warranty,
            'remaining' => $remaining,
            'status' => $warranty->status,
        ]);
    }

    public function downloadQr(Warranty $warranty)
    {
        $url = route('warranty.show', $warranty->public_token);

        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 400,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        $qrCode = (new PngWriter())->write($qrCode);

        return response($qrCode->getString())
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', "attachment; filename=\"qr-{$warranty->short_reference}.png\"");
    }

    public function printWarranty(Warranty $warranty)
    {
        if ($warranty->activated_at === null) {
            abort(404);
        }

        $pdf = Pdf::loadView('pdf.warranty-detail', [
            'warranty' => $warranty,
        ]);

        return $pdf->stream("warranty-{$warranty->short_reference}.pdf");
    }
}
