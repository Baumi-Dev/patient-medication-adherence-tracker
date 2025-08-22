<?php

use App\Models\User;
use App\Models\AdherenceLog;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public User $patient;

    public function mount(User $patient)
    {
        if (!auth()->user()->isDoctor()) {
            abort(403);
        }
        
        if (!auth()->user()->patients()->where('users.id', $patient->id)->exists()) {
            abort(403, 'Patient not assigned to you.');
        }
        
        $this->patient = $patient;
    }

    public function with(): array
    {
        return [
            'medications' => $this->patient->medications()
                ->where('active', true)
                ->withCount(['adherenceLogs as total_logs', 'adherenceLogs as taken_logs' => function ($query) {
                    $query->where('status', 'taken');
                }])
                ->get(),
            'recentLogs' => AdherenceLog::where('patient_id', $this->patient->id)
                ->with('medication')
                ->orderBy('scheduled_at', 'desc')
                ->take(10)
                ->get(),
        ];
    }
}; ?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Patient Details: :name', ['name' => $patient->name]) }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Patient Information') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Name') }}</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $patient->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Email') }}</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $patient->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Active Medications') }}</h3>
                
                @if ($medications->count() > 0)
                    <div class="space-y-4">
                        @foreach ($medications as $medication)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $medication->name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $medication->dose }} - {{ $medication->frequency }}</p>
                                        <div class="mt-2">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Times:') }}</span>
                                            @foreach ($medication->times as $time)
                                                <span class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded ml-1">{{ $time }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="ml-4 text-right">
                                        @php
                                            $adherencePercentage = $medication->total_logs > 0 ? round(($medication->taken_logs / $medication->total_logs) * 100, 1) : 0;
                                        @endphp
                                        <span class="text-lg font-bold 
                                            @if($adherencePercentage >= 80) text-green-600 dark:text-green-400
                                            @elseif($adherencePercentage >= 60) text-yellow-600 dark:text-yellow-400
                                            @else text-red-600 dark:text-red-400 @endif">
                                            {{ $adherencePercentage }}%
                                        </span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('adherence') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $medication->taken_logs }}/{{ $medication->total_logs }} {{ __('taken') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">{{ __('No active medications.') }}</p>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Recent Adherence Activity') }}</h3>
                
                @if ($recentLogs->count() > 0)
                    <div class="space-y-3">
                        @foreach ($recentLogs as $log)
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $log->medication->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $log->scheduled_at->format('M j, Y g:i A') }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($log->status === 'taken') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($log->status === 'missed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">{{ __('No adherence activity yet.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
