<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reception_table_id',
        'type',
        'primary_first_name',
        'secondary_first_name',
        'phone',
        'email',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function table()
    {
        return $this->belongsTo(ReceptionTable::class, 'reception_table_id');
    }

    public function preferences()
    {
        return $this->hasMany(GuestPreference::class);
    }

    public function beverages()
    {
        return $this->belongsToMany(Beverage::class, 'guest_preferences')->withPivot('notes')->withTimestamps();
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'couple' && $this->secondary_first_name) {
            return $this->primary_first_name.' & '.$this->secondary_first_name;
        }

        return $this->primary_first_name;
    }
}
