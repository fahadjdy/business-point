<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Shop;
use App\Models\Doctor;
use App\Models\Barber;
use App\Models\VendorOpeningTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class VendorRegistrationController extends Controller
{
    public function checkStatus()
    {
        $vendor = Vendor::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$vendor) {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $vendor->verification_status,
                'is_verified' => $vendor->is_verified,
                'reason' => $vendor->delete_reason, // Reusing delete_reason for rejection if needed, or if we add a reason field
                'business_name' => $vendor->business_name
            ]
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'type' => 'required|in:shop,doctor,barber',
            'shop_category_id' => 'nullable|required_if:type,shop|exists:shop_categories,id',
            'description' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'services' => 'nullable|string',
            'operating_days' => 'nullable|array',
            'business_hours' => 'nullable|array',
            'website' => 'nullable|url|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $service = app(\App\Services\VendorService::class);
            $vendor = $service->registerVendor($request->all());

            // Handle images if uploaded
            if ($request->hasFile('images')) {
                $mediaService = app(\App\Services\MediaService::class);
                foreach ($request->file('images') as $index => $image) {
                    $mediaService->uploadWithResize(
                        $image, 
                        $vendor, 
                        null, 
                        $index === 0 // First image is primary
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Business registration submitted successfully.',
                'data' => $vendor
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
