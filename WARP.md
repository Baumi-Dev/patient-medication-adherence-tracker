# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## About This Application

This is a **Patient Medication Adherence Tracker** built with Laravel 12, Livewire 3, and Tailwind CSS. The application helps patients track their medication schedules and allows doctors to monitor their patients' adherence patterns. It features automated medication reminders via notifications and email confirmation links.

## Development Commands

### Environment Setup
```bash
# Copy and configure environment file
cp .env.example .env

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# (Optional) Seed the database
php artisan db:seed
```

### Development Server
```bash
# Start all development services (recommended for full development)
composer dev

# Alternative: Start services individually
php artisan serve              # Laravel development server (port 8000)
npm run dev                   # Vite asset compilation and hot reload
php artisan queue:listen      # Process background jobs (medication reminders)
php artisan pail --timeout=0 # View application logs in real-time
```

### Database Management
```bash
# Reset database and run fresh migrations
php artisan migrate:fresh

# Reset database with seeding
php artisan migrate:fresh --seed

# Create a new migration
php artisan make:migration create_example_table

# Create a new model with migration and factory
php artisan make:model ExampleModel -mf
```

### Testing
```bash
# Run all tests
composer test

# Run tests with Pest directly
./vendor/bin/pest

# Run specific test file
./vendor/bin/pest tests/Feature/ExampleTest.php

# Run tests with coverage
./vendor/bin/pest --coverage
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Check code style without fixing
./vendor/bin/pint --test
```

### Background Jobs & Scheduling
```bash
# Process the job queue manually (alternative to queue:listen)
php artisan queue:work

# Schedule medication reminders for the current day
php artisan medication:schedule-reminders

# Clear all failed jobs
php artisan queue:flush
```

### Asset Management
```bash
# Build assets for production
npm run build

# Watch and compile assets during development
npm run dev
```

## Architecture Overview

### Core Domain Models
- **User**: Represents both patients and doctors with role-based access
- **Medication**: Patient medication schedules with dosage, frequency, and times
- **AdherenceLog**: Records of medication taking events (taken/missed/skipped)

### Key Relationships
- **Doctor-Patient**: Many-to-many relationship via `doctor_patient` pivot table
- **Patient-Medication**: One-to-many (patient has many medications)
- **Medication-AdherenceLog**: One-to-many (medication has many adherence logs)

### Role-Based Architecture
The application uses a simple role system with two primary roles:
- **Patient**: Can view/manage their medications and adherence history
- **Doctor**: Can view assigned patients and their adherence analytics

Role routing is handled via:
- `RoleMiddleware`: Protects routes based on user role
- Role-specific route groups (`patient.*` and `doctor.*`)
- Role-specific dashboard redirects in main dashboard route

### Frontend Stack
- **Livewire 3 + Volt**: Reactive components for dynamic UI interactions
- **Tailwind CSS**: Utility-first styling with forms plugin
- **Vite**: Fast asset compilation and hot module replacement
- **Alpine.js**: (Likely included via Livewire for frontend interactivity)

### Background Job System
The application uses Laravel's queue system for medication reminders:
- **SendMedicationReminder**: Job that creates adherence logs and sends notifications
- **ScheduleMedicationReminders**: Artisan command that dispatches reminder jobs
- **MedicationReminderNotification**: Email notifications with confirmation links

### Authentication & Authorization
- Uses Laravel Breeze for authentication scaffolding
- Custom role-based middleware for route protection
- Role checking via helper methods (`isPatient()`, `isDoctor()`) on User model

## Key Features Implementation

### Medication Reminder System
1. **Command**: `ScheduleMedicationReminders` runs daily to queue reminder jobs
2. **Job**: `SendMedicationReminder` creates adherence logs and sends notifications
3. **Notification**: Emails with signed URLs for one-click medication confirmation
4. **Route**: Public signed route for medication confirmation without authentication

### Livewire Components
Located in `resources/views/livewire/` and organized by user role:
- Patient components: medication management, adherence logging
- Doctor components: patient listing, adherence analytics
- Shared components: navigation, profile management

### Database Design
- **medications.times**: JSON field storing array of daily medication times
- **adherence_logs.status**: Enum with values: 'taken', 'missed', 'skipped'
- **adherence_logs.scheduled_at**: Precise timestamp for medication schedule
- **doctor_patient.assigned_at**: Tracks when doctor-patient relationship was established

## Testing Strategy

The application uses **Pest PHP** testing framework with:
- Feature tests for authentication flows (Laravel Breeze tests)
- Database refresh for each test via `RefreshDatabase` trait
- Custom expectation methods available in `tests/Pest.php`

## Important Development Notes

### Medication Scheduling Logic
- Medications have flexible scheduling via JSON `times` array
- Jobs are scheduled using Laravel's `delay()` method based on calculated timestamps
- Adherence logs are created as "skipped" initially, updated to "taken" via confirmation
- Duplicate adherence logs are prevented by checking existing logs for same medication/time

### Notification System
- Uses Laravel's notification system with signed URL routes
- Confirmation links work without authentication via signed routes
- Email confirmations update adherence log status from "skipped" to "taken"

### Role-Based UI Rendering
- Dashboard route automatically redirects based on user role
- Livewire components should check user roles for proper data access
- Role-specific navigation and layouts in `resources/views/` directory structure
