<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'beverage_id',
        'notes',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function beverage()
    {
        return $this->belongsTo(Beverage::class);
    }
}
