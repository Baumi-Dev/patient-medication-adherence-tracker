<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        if (auth()->user()->isPatient()) {
            return redirect()->route('patient.dashboard');
        } elseif (auth()->user()->isDoctor()) {
            return redirect()->route('doctor.dashboard');
        }

        return view('dashboard');
    })->name('dashboard');

    Route::middleware(['role:patient'])->prefix('patient')->name('patient.')->group(function () {
        Route::view('dashboard', 'patient.dashboard')->name('dashboard');
        Route::view('medications', 'patient.medications')->name('medications');
        Route::view('adherence', 'patient.adherence')->name('adherence');
    });

    Route::middleware(['role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
        Route::view('dashboard', 'doctor.dashboard')->name('dashboard');
        Route::view('patients/{patient}', 'doctor.patient-detail')->name('patient-detail');
        Route::view('analytics', 'doctor.analytics')->name('analytics');
    });
});

Route::get('medication/confirm/{log}', function (App\Models\AdherenceLog $log) {
    if (! request()->hasValidSignature()) {
        abort(401);
    }

    if ($log->status === 'skipped') {
        $log->update([
            'status' => 'taken',
            'taken_at' => now(),
        ]);

        return view('medication.confirmed', ['medication' => $log->medication]);
    }

    return view('medication.already-confirmed', ['medication' => $log->medication]);
})->name('medication.confirm');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
