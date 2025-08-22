<?php

use App\Models\Medication;
use Livewire\Volt\Component;

new class extends Component
{
    public function with(): array
    {
        return [
            'medications' => Medication::where('patient_id', auth()->id())
                ->where('active', true)
                ->orderBy('created_at', 'desc')
                ->get(),
        ];
    }

    public function deactivate($medicationId)
    {
        $medication = Medication::where('patient_id', auth()->id())->findOrFail($medicationId);
        $medication->update(['active' => false]);
        
        session()->flash('message', 'Medication deactivated successfully!');
    }

    protected $listeners = ['medication-saved' => '$refresh'];
}; ?>

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('My Medications') }}</h3>

        @if (session('message'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if ($medications->count() > 0)
            <div class="space-y-4">
                @foreach ($medications as $medication)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $medication->name }}</h4>
                                <p class="text-gray-600 dark:text-gray-400">{{ $medication->dose }} - {{ $medication->frequency }}</p>
                                <div class="mt-2">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Times:') }}</span>
                                    @foreach ($medication->times as $time)
                                        <span class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded ml-1">{{ $time }}</span>
                                    @endforeach
                                </div>
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('From') }} {{ $medication->start_date->format('M j, Y') }}
                                    @if ($medication->end_date)
                                        {{ __('to') }} {{ $medication->end_date->format('M j, Y') }}
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Adherence:') }} {{ $medication->adherence_percentage }}%
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <x-danger-button wire:click="deactivate({{ $medication->id }})" onclick="return confirm('Are you sure you want to deactivate this medication?')">
                                    {{ __('Deactivate') }}
                                </x-danger-button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">{{ __('No medications added yet.') }}</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">{{ __('Click "Add Medication" above to get started.') }}</p>
            </div>
        @endif
    </div>
</div>
