<?php

namespace App\Http\Controllers;

use App\Models\Library;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\SubscriptionHistory;
use Illuminate\Http\Request;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRGdImagePNG;

class LibraryController extends Controller
{
    public function index()
    {
        return Library::with('user')->latest()->get();
    }

    public function store(Request $request)
    {
        $createNewUser = $request->boolean('create_new_user');

        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'valid_upto' => 'required|date',
            'code' => 'required|string|unique:libraries,code|max:4',
            'logo' => 'nullable|image|max:2048',
            // 'no_of_tables' => 'nullable|integer',
            'plan_id' => 'required|exists:subscription_plans,id',
        ];

        if ($createNewUser) {
            $rules['owner_name'] = 'required|string|max:255';
            $rules['password'] = 'required|string|min:6';
            $rules['user_suffix'] = 'required|string|max:4';
        }
        else {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $validated, $createNewUser) {
            $userId = $validated['user_id'] ?? null;

            if ($createNewUser) {
                $loginName = $validated['code'] . $validated['user_suffix'];

                // Double check login name uniqueness
                if (\App\Models\User::where('login_name', $loginName)->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'user_suffix' => ['The generated User ID (Code + Suffix) is already taken.'],
                    ]);
                }

                $user = \App\Models\User::create([
                    'name' => $validated['owner_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'login_name' => $loginName,
                    'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                    'role' => 'library',
                    'library_code' => $validated['code'],
                ]);
                $userId = $user->id;
            }

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('libraries', 'public');
                $validated['logo'] = $path;
            }

            $library = \App\Models\Library::create([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'latitude' => $validated['latitude'] ?? 0,
                'longitude' => $validated['longitude'] ?? 0,
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'valid_upto' => $validated['valid_upto'],
                'code' => $validated['code'],
                'user_id' => $userId,
                'logo' => $validated['logo'] ?? null,
                // 'no_of_tables' => $validated['no_of_tables'] ?? 0,
                'subscription_plan_id' => $validated['plan_id'],
            ]);

            // Create a sub record
            \App\Models\SubscriptionHistory::create([
                'library_id' => $library->id,
                'subscription_plan_id' => $validated['plan_id'],
                'amount' => \App\Models\SubscriptionPlan::find($validated['plan_id'])->trade_amount,
                'is_paid' => true,
            ]);

            return response()->json($library->load('user'), 201);
        });
    }

    public function show(Library $library)
    {
        return $library->load('user');
    }

    public function update(Request $request, Library $library)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'address' => 'string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'valid_upto' => 'date',
            'code' => 'string|unique:libraries,code,' . $library->id,
            'user_id' => 'exists:users,id',
            'logo' => 'nullable|image|max:2048',
            // 'no_of_tables' => 'nullable|integer',
            'plan_id' => 'nullable|exists:subscription_plans,id',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('libraries', 'public');
            $validated['logo'] = $path;
        }

        if (isset($validated['plan_id'])) {
            $validated['subscription_plan_id'] = $validated['plan_id'];
            unset($validated['plan_id']);
        }

        $library->update($validated);

        return response()->json($library->load('user'));
    }

    public function destroy(Library $library)
    {
        $library->delete();
        return response()->noContent();
    }

    public function getSubscriptionPlans()
    {
        return SubscriptionPlan::all();
    }

    public function updateNoOfSeats(Request $request, Library $library)
    {
        $validated = $request->validate([
            'no_of_tables' => 'required|integer',
        ]);

        $library->update($validated);

        return response()->json($library);
    }

    public function generateQrCodeLabel(Library $library)
    {
        $baseImagePath = public_path('images/qr_base.png');
        if (!file_exists($baseImagePath)) {
            return response()->json(['error' => 'Base image not found'], 404);
        }

        // 1. Create base image from PNG
        $baseImage = imagecreatefrompng($baseImagePath);
        if (!$baseImage) {
            return response()->json(['error' => 'Could not load base image'], 500);
        }

        // $primaryColor = imagecolorallocate($baseImage, 245, 48, 3); // #f53003

        // 2. Generate QR Code
        // Using chillerlan/php-qrcode which works with GD and doesn't need imagick
        $options = new QROptions([
            'version' => 7,
            'outputInterface' => QRGdImagePNG::class ,
            'scale' => 10,
            'imageTransparent' => false,
            'drawCircularModules' => false,
            'keepAlive' => true,
            'returnResource' => true,
            'quietzoneSize' => 0, // 🔥 removes padding
        ]);

        $qrcode = new QRCode($options);
        $srcImage = $qrcode->render($library->code);

        // 3. Merge QR Code onto base image
        // We now have a GdImage object directly from the renderer
        $srcW = imagesx($srcImage);
        $srcH = imagesy($srcImage);

        // We want the destination to be roughly 400x400
        $dstW = 430;
        $dstH = 430;
        $qrX = (595 - $dstW) / 2;
        $qrY = 230;

        imagecopyresampled($baseImage, $srcImage, $qrX, $qrY, 0, 0, $dstW, $dstH, $srcW, $srcH);

        // 4. Add Library Name
        $whiteColor = imagecolorallocate($baseImage, 255, 255, 255);
        $fontPath = '/System/Library/Fonts/Supplemental/Arial Bold.ttf'; // Bold for the title
        if (!file_exists($fontPath)) {
            $fontPath = '/System/Library/Fonts/Supplemental/Arial.ttf';
        }

        if (file_exists($fontPath)) {
            $fontSize = 32;
            $text = strtoupper($library->name);

            // Calculate text box for center alignment
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
            $textWidth = $bbox[2] - $bbox[0];
            $textX = (595 - $textWidth) / 2;
            $textY = 55; // Centered in the top header area
            imagettftext($baseImage, $fontSize, 0, $textX, $textY, $whiteColor, $fontPath, $text);

        // Add Library ID Code at the bottom in primary color
        // $codeText = "CODE: " . $library->code;
        // $codeFontSize = 18;
        // $bbox = imagettfbbox($codeFontSize, 0, $fontPath, $codeText);
        // $codeWidth = $bbox[2] - $bbox[0];
        // $codeX = (595 - $codeWidth) / 2;
        // $codeY = 750; // White space below QR
        // imagettftext($baseImage, $codeFontSize, 0, $codeX, $codeY, $primaryColor, $fontPath, $codeText);
        }
        else {
            // Basic fallback without TTF
            $text = strtoupper($library->name);
            imagestring($baseImage, 5, (595 - (strlen($text) * 9)) / 2, 80, $text, $whiteColor);
        }

        // 5. Output image
        return response()->stream(function () use ($baseImage) {
            imagepng($baseImage);
            imagedestroy($baseImage);
        }, 200, ['Content-Type' => 'image/png']);
    }
}