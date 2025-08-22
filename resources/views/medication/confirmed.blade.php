<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }} - Medication Confirmed</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <div class="min-h-screen flex items-center justify-center">
            <div class="max-w-md w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Medication Confirmed!') }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Thank you for confirming that you took your medication: :name', ['name' => $medication->name]) }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">
                        {{ __('Confirmed at: :time', ['time' => now()->format('M j, Y g:i A')]) }}
                    </p>
                    <div class="mt-6">
                        <p class="text-xs text-gray-400 dark:text-gray-600">
                            {{ __('You can close this window now.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
