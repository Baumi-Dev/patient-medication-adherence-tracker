<?php

namespace App\Console\Commands;

use App\Jobs\SendMedicationReminder;
use App\Models\Medication;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScheduleMedicationReminders extends Command
{
    protected $signature = 'medication:schedule-reminders';

    protected $description = 'Schedule medication reminders for today';

    public function handle()
    {
        $today = Carbon::today();

        $medications = Medication::where('active', true)
            ->where('start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->get();

        $scheduledCount = 0;

        foreach ($medications as $medication) {
            foreach ($medication->times as $time) {
                $scheduledAt = $today->copy()->setTimeFromTimeString($time);

                if ($scheduledAt->isFuture()) {
                    SendMedicationReminder::dispatch($medication, $time)
                        ->delay($scheduledAt);
                    $scheduledCount++;
                }
            }
        }

        $this->info("Scheduled {$scheduledCount} medication reminders for today.");

        return 0;
    }
}
