<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'name',
        'dose',
        'frequency',
        'times',
        'start_date',
        'end_date',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'times' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'active' => 'boolean',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function adherenceLogs()
    {
        return $this->hasMany(AdherenceLog::class);
    }

    public function getAdherencePercentageAttribute()
    {
        $totalLogs = $this->adherenceLogs()->count();
        if ($totalLogs === 0) {
            return 0;
        }

        $takenLogs = $this->adherenceLogs()->where('status', 'taken')->count();

        return round(($takenLogs / $totalLogs) * 100, 2);
    }
}
