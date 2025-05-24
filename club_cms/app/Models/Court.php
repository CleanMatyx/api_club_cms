<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
