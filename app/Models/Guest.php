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
        'phone',
        'email',
        'invitation_token',
        'rsvp_status',
        'rsvp_confirmed_at',
        'whatsapp_sent_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'rsvp_confirmed_at' => 'datetime',
        'whatsapp_sent_at' => 'datetime',
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
        return $this->primary_first_name;
    }

    /**
     * Relation avec les notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
