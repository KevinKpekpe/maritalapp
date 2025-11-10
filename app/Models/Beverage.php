<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Beverage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function guestPreferences()
    {
        return $this->hasMany(GuestPreference::class);
    }

    public function guests()
    {
        return $this->belongsToMany(Guest::class, 'guest_preferences')->withPivot('notes')->withTimestamps();
    }
}
