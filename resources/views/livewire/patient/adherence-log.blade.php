<?php

use App\Models\AdherenceLog;
use App\Models\Medication;
use Livewire\Volt\Component;

new class extends Component
{
    public function with(): array
    {
        return [
            'adherenceLogs' => AdherenceLog::where('patient_id', auth()->id())
                ->with('medication')
                ->orderBy('scheduled_at', 'desc')
                ->paginate(20),
            'medications' => Medication::where('patient_id', auth()->id())
                ->where('active', true)
                ->get(),
        ];
    }

    public function markAsTaken($logId)
    {
        $log = AdherenceLog::where('patient_id', auth()->id())->findOrFail($logId);
        $log->update([
            'status' => 'taken',
            'taken_at' => now(),
        ]);
        
        session()->flash('message', 'Medication marked as taken!');
    }

    public function markAsMissed($logId)
    {
        $log = AdherenceLog::where('patient_id', auth()->id())->findOrFail($logId);
        $log->update(['status' => 'missed']);
        
        session()->flash('message', 'Medication marked as missed.');
    }
}; ?>

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Adherence History') }}</h3>

        @if (session('message'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if ($adherenceLogs->count() > 0)
            <div class="space-y-4">
                @foreach ($adherenceLogs as $log)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $log->medication->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $log->medication->dose }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('Scheduled:') }} {{ $log->scheduled_at->format('M j, Y g:i A') }}
                                </p>
                                @if ($log->taken_at)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Taken:') }} {{ $log->taken_at->format('M j, Y g:i A') }}
                                    </p>
                                @endif
                            </div>
                            <div class="ml-4 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($log->status === 'taken') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($log->status === 'missed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                    {{ ucfirst($log->status) }}
                                </span>
                                
                                @if ($log->status === 'skipped' && $log->scheduled_at->isFuture())
                                    <x-secondary-button wire:click="markAsTaken({{ $log->id }})" class="text-xs">
                                        {{ __('Mark Taken') }}
                                    </x-secondary-button>
                                    <x-danger-button wire:click="markAsMissed({{ $log->id }})" class="text-xs">
                                        {{ __('Mark Missed') }}
                                    </x-danger-button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                {{ $adherenceLogs->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">{{ __('No adherence logs yet.') }}</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">{{ __('Logs will appear here as you track your medications.') }}</p>
            </div>
        @endif
    </div>
</div>
