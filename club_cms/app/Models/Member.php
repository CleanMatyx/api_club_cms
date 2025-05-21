<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    /**
     * Enable the use of the HasFactory trait.
     */
    use HasFactory;

    /**
     * The name of the table associated with the model.
     */
    protected $table = 'members';
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'membership_date',
        'status',
    ];

    /**
     * Get the reservations associated with the member.
     */
    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Find if the member has less than 3 reservations for a given date.
     */
    public function checkTotalReservations(Carbon $date) {
        return $this->reservations()->whereDate('date', $date)->count() < 3;
    }

    /**
     * Check if the member has a reservation for the same hour on a given date and hour.
     */
    public function checkSameHourReservation(Carbon $date, $hour) {
        return $this->reservations()->whereDate('date', $date)->where('hour', $hour)->exists();
    }
}
