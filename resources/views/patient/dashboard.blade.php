<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Patient Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">{{ __('Welcome back!') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('Manage your medications and track your adherence.') }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <a href="{{ route('patient.medications') }}" class="block p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                            <h4 class="text-lg font-medium text-blue-900 dark:text-blue-100">{{ __('My Medications') }}</h4>
                            <p class="text-blue-700 dark:text-blue-300">{{ __('Add and manage your medications') }}</p>
                        </a>
                        
                        <a href="{{ route('patient.adherence') }}" class="block p-6 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition">
                            <h4 class="text-lg font-medium text-green-900 dark:text-green-100">{{ __('Adherence Log') }}</h4>
                            <p class="text-green-700 dark:text-green-300">{{ __('View your medication history') }}</p>
                        </a>
                    </div>
                </div>
            </div>

            <livewire:patient.recent-medications />
        </div>
    </div>
</x-app-layout>
