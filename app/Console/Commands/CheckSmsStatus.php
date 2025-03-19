<?php

namespace App\Console\Commands;

use App\Models\SMSNotification;
use App\Services\SmsService;
use Illuminate\Console\Command;

class CheckSmsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-sms-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $smsService = new SmsService();

        // Get all SMS logs with pending status
        $smsLogs = SMSNotification::where('status', 'pending')->get();

        foreach ($smsLogs as $log) {
            if ($log->message_id) {
                $statusResponse = $smsService->getSmsStatus($log->message_id);

                if (isset($statusResponse['status'])) {
                    // Update status in the database
                    $log->update(['status' => $statusResponse['status']]);

                    $this->info("Updated SMS ID {$log->message_id} to status: {$statusResponse['status']}");
                }
            }
        }
    }
}
