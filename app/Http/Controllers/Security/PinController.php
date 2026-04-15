<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Security\PinService;

class PinController extends Controller
{
    private $pinService;

    public function __construct(PinService $pinService)
    {
        $this->pinService = $pinService;
    }

    // 🔐 SET PIN
    public function setPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4|confirmed'
        ]);

        $this->pinService->setPin($request->user(), $request->pin);

        return response()->json([
            'status' => true,
            'message' => 'PIN set successfully'
        ]);
    }

    // 🔍 VERIFY PIN
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4'
        ]);

        return response()->json(
            $this->pinService->verifyPin($request->user(), $request->pin)
        );
    }
}