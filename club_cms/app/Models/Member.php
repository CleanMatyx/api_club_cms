<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

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
        'name',
        'email',
        'phone',
        'user_id',
        'membership_date',
        'status',
        'role',
    ];

    /**
     * Get the reservations associated with the member.
     */
    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Check if the member can make a reservation on a given date.
     */
    public function checkMaxReservations(Carbon $date) {
        $reservationsCount = $this->reservations()->whereDate('date', $date)->count();
        return $reservationsCount < 3;
    }        

    /**
     * Get the user associated with the member.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the member has a reservation for the same court, date and hour.
     */
    public function checkReservationsSameDate($courtId, Carbon $date, $hour) {
        $existingReservation = $this->reservations()
            ->where('court_id', $courtId)
            ->whereDate('date', $date)
            ->whereTime('hour', $hour)
            ->first();
        
        return $existingReservation === null;
    }
}
