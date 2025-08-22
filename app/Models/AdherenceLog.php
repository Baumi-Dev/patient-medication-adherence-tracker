<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdherenceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id',
        'patient_id',
        'status',
        'scheduled_at',
        'taken_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'taken_at' => 'datetime',
        ];
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
