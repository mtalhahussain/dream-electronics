<?php

namespace App\Jobs;

use App\Models\Installment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;
use Carbon\Carbon;

class SendInstallmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Installment $installment;

    public function __construct(Installment $installment)
    {
        $this->installment = $installment;
    }

    public function handle(): void
    {
        try {
            // Load the installment with sale and customer
            $this->installment->load('sale.customer');
            
            $customer = $this->installment->sale->customer;
            $dueDate = Carbon::parse($this->installment->due_date);
            $amount = number_format((float) $this->installment->amount, 2);

            $companyName = \App\Models\Setting::get('company_name', 'Dream Electronics');
            $message = "Dear {$customer->name}, your installment of Rs. {$amount} is due on {$dueDate->format('d-M-Y')} for Sale #{$this->installment->sale->id}. Please ensure timely payment. - {$companyName}";

            // Initialize Twilio client
            $twilio = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            // Send SMS
            $twilio->messages->create(
                $customer->phone,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message
                ]
            );

            \Log::info("SMS reminder sent for installment ID: {$this->installment->id}");

        } catch (\Exception $e) {
            \Log::error("Failed to send SMS reminder for installment ID: {$this->installment->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }
}