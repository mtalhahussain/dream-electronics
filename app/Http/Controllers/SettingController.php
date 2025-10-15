<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-settings');
    }

    public function index()
    {
        $settings = [
            'company_name' => Setting::get('company_name', config('app.name')),
            'company_address' => Setting::get('company_address', ''),
            'company_phone' => Setting::get('company_phone', ''),
            'company_email' => Setting::get('company_email', ''),
            'twilio_sid' => Setting::get('twilio_sid', ''),
            'twilio_token' => Setting::get('twilio_token', ''),
            'twilio_from' => Setting::get('twilio_from', ''),
            'default_installment_months' => Setting::get('default_installment_months', '12'),
            'late_fee_percentage' => Setting::get('late_fee_percentage', '5'),
            'currency_symbol' => Setting::get('currency_symbol', 'PKR'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'twilio_sid' => 'nullable|string|max:255',
            'twilio_token' => 'nullable|string|max:255',
            'twilio_from' => 'nullable|string|max:20',
            'default_installment_months' => 'required|integer|min:1|max:60',
            'late_fee_percentage' => 'required|numeric|min:0|max:100',
            'currency_symbol' => 'required|string|max:10',
        ]);

        try {
            foreach ($request->all() as $key => $value) {
                if ($key !== '_token' && $key !== '_method') {
                    Setting::set($key, $value);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testTwilio(): JsonResponse
    {
        try {
            $sid = Setting::get('twilio_sid');
            $token = Setting::get('twilio_token');
            $from = Setting::get('twilio_from');

            if (!$sid || !$token || !$from) {
                return response()->json([
                    'success' => false,
                    'message' => 'Twilio credentials not configured'
                ]);
            }

            $twilio = new \Twilio\Rest\Client($sid, $token);
            
            // Try to fetch account info to test credentials
            $account = $twilio->api->v2010->accounts($sid)->fetch();
            
            return response()->json([
                'success' => true,
                'message' => 'Twilio connection successful! Account: ' . $account->friendlyName
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Twilio connection failed: ' . $e->getMessage()
            ]);
        }
    }
}
