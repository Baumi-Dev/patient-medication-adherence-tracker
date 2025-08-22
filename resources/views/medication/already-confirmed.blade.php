<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }} - Already Confirmed</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <div class="min-h-screen flex items-center justify-center">
            <div class="max-w-md w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Already Confirmed') }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('This medication has already been confirmed: :name', ['name' => $medication->name]) }}
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
