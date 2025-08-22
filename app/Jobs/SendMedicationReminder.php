<?php

namespace App\Jobs;

use App\Models\AdherenceLog;
use App\Models\Medication;
use App\Notifications\MedicationReminderNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMedicationReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Medication $medication,
        public string $scheduledTime
    ) {}

    public function handle(): void
    {
        $scheduledAt = Carbon::today()->setTimeFromTimeString($this->scheduledTime);

        $existingLog = AdherenceLog::where('medication_id', $this->medication->id)
            ->where('patient_id', $this->medication->patient_id)
            ->whereDate('scheduled_at', $scheduledAt->toDateString())
            ->whereTime('scheduled_at', $scheduledAt->toTimeString())
            ->first();

        if (! $existingLog) {
            $log = AdherenceLog::create([
                'medication_id' => $this->medication->id,
                'patient_id' => $this->medication->patient_id,
                'status' => 'skipped',
                'scheduled_at' => $scheduledAt,
            ]);

            $this->medication->patient->notify(
                new MedicationReminderNotification($this->medication, $log)
            );
        }
    }
}
