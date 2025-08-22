<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component
{
    public function with(): array
    {
        $doctor = auth()->user();
        
        return [
            'patients' => $doctor->patients()
                ->withCount(['medications' => function ($query) {
                    $query->where('active', true);
                }])
                ->get(),
            'allPatients' => User::where('role', 'patient')
                ->whereNotIn('id', $doctor->patients()->pluck('users.id'))
                ->get(),
        ];
    }

    public function assignPatient($patientId)
    {
        $doctor = auth()->user();
        $doctor->patients()->attach($patientId, ['assigned_at' => now()]);
        
        session()->flash('message', 'Patient assigned successfully!');
    }

    public function unassignPatient($patientId)
    {
        $doctor = auth()->user();
        $doctor->patients()->detach($patientId);
        
        session()->flash('message', 'Patient unassigned successfully!');
    }
}; ?>

<div class="space-y-6">
    @if (session('message'))
        <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('My Patients') }}</h3>

            @if ($patients->count() > 0)
                <div class="space-y-4">
                    @foreach ($patients as $patient)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $patient->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $patient->email }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ __('Active Medications:') }} {{ $patient->medications_count }}
                                    </p>
                                </div>
                                <div class="ml-4 flex items-center space-x-2">
                                    <a href="{{ route('doctor.patient-detail', $patient) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium">
                                        {{ __('View Details') }}
                                    </a>
                                    <x-danger-button wire:click="unassignPatient({{ $patient->id }})" onclick="return confirm('Are you sure you want to unassign this patient?')" class="text-xs">
                                        {{ __('Unassign') }}
                                    </x-danger-button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('No patients assigned yet.') }}</p>
                </div>
            @endif
        </div>
    </div>

    @if ($allPatients->count() > 0)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Available Patients') }}</h3>
                
                <div class="space-y-3">
                    @foreach ($allPatients as $patient)
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $patient->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $patient->email }}</p>
                            </div>
                            <x-primary-button wire:click="assignPatient({{ $patient->id }})" class="text-xs">
                                {{ __('Assign') }}
                            </x-primary-button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
