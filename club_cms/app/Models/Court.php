<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Court extends Model
{
    /**
     * Enable the use of the HasFactory trait.
     */
    use HasFactory;

    /**
     * The name of the table associated with the model.
     */
    protected $table = 'courts';
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sport_id',
        'name',
        'location',
    ];

    /**
     * Get the sport associated with the court.
     */
    public function sport() {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get the reservations associated with the court.
     */
    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Check if the court is available for a given date and time.
     */
    public function checkAvailability($date, $time) {
        return !$this->reservations()
            ->where('date', $date)
            ->where('hour', $time)
            ->exists();
    }
    
    /**
     * Get the available hours for a given date.
     */
    public function availableHoursOfDay(Carbon $date){
        $bookingHours = [];

        $bookings = $this->reservations()
            ->whereDate('date', $date)
            ->get();

        foreach ($bookings as $booking) {
            $bookingDate = Carbon::createFromFormat('Y-m-d H:i:s', $booking['date'] . ' ' . $booking['hour']);
            $bookingHours[] = $bookingDate->hour;
        }

        $hours = range(8, 21);

        return array_map(function ($hour) {
            return Carbon::createFromTime($hour, 0)->format('H:i');
        }, array_diff($hours, $bookingHours));
    }
}
