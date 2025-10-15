<?php

namespace App\Console\Commands;

use App\Jobs\SendInstallmentReminderJob;
use App\Models\Installment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendInstallmentReminders extends Command
{
    protected $signature = 'installments:remind';
    protected $description = 'Send SMS reminders for installments due in 3 days';

    public function handle(): int
    {
        $this->info('Starting installment reminder process...');

        $threeDaysFromNow = Carbon::now()->addDays(3)->toDateString();

        // Get installments due in 3 days that are unpaid or partially paid
        $installments = Installment::with('sale.customer')
            ->where('due_date', $threeDaysFromNow)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get();

        $count = 0;

        foreach ($installments as $installment) {
            try {
                // Dispatch the SMS job
                SendInstallmentReminderJob::dispatch($installment);
                $count++;
                
                $this->info("Queued reminder for installment ID: {$installment->id} - Customer: {$installment->sale->customer->name}");
                
            } catch (\Exception $e) {
                $this->error("Failed to queue reminder for installment ID: {$installment->id} - Error: " . $e->getMessage());
            }
        }

        $this->info("Completed! Queued {$count} reminder(s) for installments due on {$threeDaysFromNow}");

        return Command::SUCCESS;
    }
}