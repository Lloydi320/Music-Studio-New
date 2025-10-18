<?php

namespace App\Http\Controllers;

use App\Models\RehearsalQrConfig;
use App\Models\RentalQrConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QrConfigAdminController extends Controller
{
    public function index()
    {
        $rehearsalConfigs = RehearsalQrConfig::orderBy('duration_minutes')->get();
        $rentalConfigs = RentalQrConfig::orderBy('rental_type')->get();

        return view('admin.qr_configs', [
            'rehearsalConfigs' => $rehearsalConfigs,
            'rentalConfigs' => $rentalConfigs,
        ]);
    }

    public function storeRehearsal(Request $request)
    {
        $validated = $request->validate([
            'duration_minutes' => ['required', 'integer', 'in:60,120,180,240,300,360,420,480'],
            'reservation_fee_php' => ['required', 'numeric', 'min:0'],
            'qr_image' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
        ]);

        $duration = (int) $validated['duration_minutes'];
        $fee = (float) $validated['reservation_fee_php'];

        $file = $validated['qr_image'];
        $ext = $file->getClientOriginalExtension();
        $filename = $duration . '.' . $ext;
        $relativePath = 'qr/rehearsal/' . $filename;
        Storage::disk('public')->putFileAs('qr/rehearsal', $file, $filename);

        $userId = Auth::id();
        $config = RehearsalQrConfig::updateOrCreate(
            ['duration_minutes' => $duration],
            [
                'reservation_fee_php' => $fee,
                'qr_image_path' => $relativePath,
                'enabled' => true,
                'updated_by' => $userId,
                'created_by' => $userId,
            ]
        );

        $hours = (int) ($duration / 60);
        return redirect()->back()->with('status', 'Rehearsal QR updated for ' . $hours . ' ' . ($hours === 1 ? 'hour' : 'hours') . '.');
    }

    public function storeRental(Request $request)
    {
        $validated = $request->validate([
            'rental_type' => ['required', 'in:instruments,full_package'],
            'reservation_fee_php' => ['required', 'numeric', 'min:0'],
            'qr_image' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
        ]);

        $type = $validated['rental_type'];
        $fee = (float) $validated['reservation_fee_php'];

        $file = $validated['qr_image'];
        $ext = $file->getClientOriginalExtension();
        $filename = $type . '.' . $ext;
        $relativePath = 'qr/rental/' . $filename;
        Storage::disk('public')->putFileAs('qr/rental', $file, $filename);

        RentalQrConfig::updateOrCreate(
            ['rental_type' => $type],
            [
                'reservation_fee_php' => $fee,
                'qr_image_path' => $relativePath,
                'enabled' => true,
            ]
        );

        return redirect()->back()->with('status', 'Rental QR updated for ' . $type . '.');
    }
}