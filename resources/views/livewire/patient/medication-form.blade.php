<?php

use App\Models\Medication;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $dose = '';
    public string $frequency = 'daily';
    public array $times = ['08:00'];
    public string $start_date = '';
    public string $end_date = '';
    public bool $showForm = false;

    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
    }

    public function addTime()
    {
        $this->times[] = '08:00';
    }

    public function removeTime($index)
    {
        unset($this->times[$index]);
        $this->times = array_values($this->times);
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'dose' => 'required|string|max:255',
            'frequency' => 'required|string|max:255',
            'times' => 'required|array|min:1',
            'times.*' => 'required|date_format:H:i',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        Medication::create([
            'patient_id' => auth()->id(),
            'name' => $validated['name'],
            'dose' => $validated['dose'],
            'frequency' => $validated['frequency'],
            'times' => $validated['times'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        $this->reset(['name', 'dose', 'frequency', 'times', 'end_date']);
        $this->times = ['08:00'];
        $this->start_date = now()->format('Y-m-d');
        $this->showForm = false;

        $this->dispatch('medication-saved');
        session()->flash('message', 'Medication added successfully!');
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }
}; ?>

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add New Medication') }}</h3>
            <x-primary-button wire:click="toggleForm">
                {{ $showForm ? __('Cancel') : __('Add Medication') }}
            </x-primary-button>
        </div>

        @if (session('message'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if ($showForm)
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="name" :value="__('Medication Name')" />
                        <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="dose" :value="__('Dose')" />
                        <x-text-input wire:model="dose" id="dose" class="block mt-1 w-full" type="text" placeholder="e.g., 10mg" required />
                        <x-input-error :messages="$errors->get('dose')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="frequency" :value="__('Frequency')" />
                    <select wire:model="frequency" id="frequency" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="daily">{{ __('Daily') }}</option>
                        <option value="twice daily">{{ __('Twice Daily') }}</option>
                        <option value="three times daily">{{ __('Three Times Daily') }}</option>
                        <option value="weekly">{{ __('Weekly') }}</option>
                        <option value="as needed">{{ __('As Needed') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('frequency')" class="mt-2" />
                </div>

                <div>
                    <x-input-label :value="__('Times')" />
                    <div class="mt-2 space-y-2">
                        @foreach ($times as $index => $time)
                            <div class="flex items-center space-x-2">
                                <x-text-input wire:model="times.{{ $index }}" type="time" class="block w-32" />
                                @if (count($times) > 1)
                                    <x-danger-button type="button" wire:click="removeTime({{ $index }})">
                                        {{ __('Remove') }}
                                    </x-danger-button>
                                @endif
                            </div>
                        @endforeach
                        <x-secondary-button type="button" wire:click="addTime">
                            {{ __('Add Time') }}
                        </x-secondary-button>
                    </div>
                    <x-input-error :messages="$errors->get('times')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="start_date" :value="__('Start Date')" />
                        <x-text-input wire:model="start_date" id="start_date" class="block mt-1 w-full" type="date" required />
                        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="end_date" :value="__('End Date (Optional)')" />
                        <x-text-input wire:model="end_date" id="end_date" class="block mt-1 w-full" type="date" />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-primary-button>
                        {{ __('Save Medication') }}
                    </x-primary-button>
                </div>
            </form>
        @endif
    </div>
</div>
