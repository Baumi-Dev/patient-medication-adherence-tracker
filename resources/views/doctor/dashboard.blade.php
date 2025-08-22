<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Doctor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">{{ __('Patient Overview') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('Monitor your patients\' medication adherence.') }}</p>
                </div>
            </div>

            <livewire:doctor.patient-list />
        </div>
    </div>
</x-app-layout>
