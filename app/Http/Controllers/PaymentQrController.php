<?php

namespace App\Http\Controllers;

use App\Models\RehearsalQrConfig;
use App\Models\RentalQrConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentQrController extends Controller
{
    /**
     * GET /api/payment-qr/rehearsal?duration=90
     */
    public function rehearsal(Request $request)
    {
        $duration = (int) $request->query('duration');
        if (!$duration) {
            return response()->json(['error' => 'Missing duration'], 422);
        }

        $config = RehearsalQrConfig::where('duration_minutes', $duration)
            ->where('enabled', true)
            ->first();

        if (!$config) {
            return response()->json(['error' => 'No QR configured for selected duration'], 404);
        }

        return response()->json([
            'qr_url' => Storage::disk('public')->url($config->qr_image_path),
            'reservation_fee_php' => (float) $config->reservation_fee_php,
            'duration_minutes' => $config->duration_minutes,
            'valid_from' => $config->valid_from,
            'valid_to' => $config->valid_to,
        ]);
    }

    /**
     * GET /api/payment-qr/rental?type=instruments|full_package
     */
    public function rental(Request $request)
    {
        $type = (string) $request->query('type');
        if (!$type) {
            return response()->json(['error' => 'Missing rental type'], 422);
        }
        if (!in_array($type, ['instruments', 'full_package'], true)) {
            return response()->json(['error' => 'Invalid rental type'], 422);
        }

        $config = RentalQrConfig::where('rental_type', $type)
            ->where('enabled', true)
            ->first();

        if (!$config) {
            return response()->json(['error' => 'No QR configured for instruments/full_package'], 404);
        }

        return response()->json([
            'qr_url' => Storage::disk('public')->url($config->qr_image_path),
            'reservation_fee_php' => (float) $config->reservation_fee_php,
            'rental_type' => $config->rental_type,
        ]);
    }
}