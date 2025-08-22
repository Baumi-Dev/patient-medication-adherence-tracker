<?php

use App\Models\Medication;
use Livewire\Volt\Component;

new class extends Component
{
    public function with(): array
    {
        return [
            'recentMedications' => Medication::where('patient_id', auth()->id())
                ->where('active', true)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get(),
        ];
    }
}; ?>

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Recent Medications') }}</h3>

        @if ($recentMedications->count() > 0)
            <div class="space-y-3">
                @foreach ($recentMedications as $medication)
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $medication->name }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $medication->dose }} - {{ $medication->frequency }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $medication->adherence_percentage }}%
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('adherence') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4 text-center">
                <a href="{{ route('patient.medications') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium">
                    {{ __('View all medications →') }}
                </a>
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-gray-500 dark:text-gray-400">{{ __('No medications added yet.') }}</p>
                <a href="{{ route('patient.medications') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium">
                    {{ __('Add your first medication →') }}
                </a>
            </div>
        @endif
    </div>
</div>
