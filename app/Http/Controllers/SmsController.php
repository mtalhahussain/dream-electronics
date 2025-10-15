<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Jobs\SendInstallmentReminderJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Twilio\Rest\Client;

class SmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view-sms')->only(['index', 'logs']);
        $this->middleware('can:send-sms')->only(['send', 'sendBulk', 'sendReminder']);
    }

    public function index()
    {
        // Get recent SMS activities or pending reminders
        $pending_reminders = Installment::with('sale.customer')
            ->where('due_date', '>=', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(7))
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date')
            ->paginate(10);

        $customers = Customer::orderBy('name')->get();

        return view('sms.index', compact('pending_reminders', 'customers'));
    }

    public function logs()
    {
        $sms_logs = SmsLog::with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('sms.logs', compact('sms_logs'));
    }

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'message' => 'required|string|max:1600',
        ]);

        try {
            $customer = Customer::findOrFail($request->customer_id);
            
            $result = $this->sendSmsToCustomer($customer, $request->message);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully to ' . $customer->name
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendBulk(Request $request): JsonResponse
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
            'message' => 'required|string|max:1600',
        ]);

        try {
            $customers = Customer::whereIn('id', $request->customer_ids)->get();
            $sent = 0;
            $failed = 0;

            foreach ($customers as $customer) {
                $result = $this->sendSmsToCustomer($customer, $request->message);
                if ($result['success']) {
                    $sent++;
                } else {
                    $failed++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "SMS sent to {$sent} customers. {$failed} failed."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send bulk SMS: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendReminder(Request $request): JsonResponse
    {
        $request->validate([
            'installment_id' => 'required|exists:installments,id'
        ]);

        try {
            $installment = Installment::with('sale.customer')->findOrFail($request->installment_id);
            $customer = $installment->sale->customer;
            
            $message = "Dear {$customer->name}, your installment of PKR " . number_format((float)$installment->amount) . 
                      " is due on " . $installment->due_date->format('d M Y') . 
                      ". Please make payment on time. Thanks - " . config('app.name');
            
            $result = $this->sendSmsToCustomer($customer, $message, 'reminder', $installment->id);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reminder sent successfully to ' . $customer->name
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    private function sendSmsToCustomer($customer, $message, $type = 'manual', $reference_id = null): array
    {
        try {
            // Get Twilio credentials from settings
            $sid = Setting::get('twilio_sid');
            $token = Setting::get('twilio_token');
            $from = Setting::get('twilio_from');

            if (!$sid || !$token || !$from) {
                return [
                    'success' => false,
                    'message' => 'Twilio credentials not configured'
                ];
            }

            $twilio = new Client($sid, $token);
            
            $twilioMessage = $twilio->messages->create(
                $customer->phone,
                [
                    'from' => $from,
                    'body' => $message
                ]
            );

            // Log the SMS
            SmsLog::create([
                'customer_id' => $customer->id,
                'phone' => $customer->phone,
                'message' => $message,
                'type' => $type,
                'reference_id' => $reference_id,
                'status' => 'sent',
                'twilio_sid' => $twilioMessage->sid,
                'sent_at' => Carbon::now()
            ]);

            return ['success' => true];
        } catch (\Exception $e) {
            // Log failed SMS
            SmsLog::create([
                'customer_id' => $customer->id,
                'phone' => $customer->phone,
                'message' => $message,
                'type' => $type,
                'reference_id' => $reference_id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => Carbon::now()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}