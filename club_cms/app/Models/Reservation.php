<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    /**
     * Enable the use of the HasFactory trait.
     */
    use HasFactory;

    /**
     * The name of the table associated with the model.
     */
    protected $table = 'reservations';
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'member_id',
        'court_id',
        'date',
        'hour'
    ];

    /**
     * Get the member associated with the reservation.
     */
    public function member() {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the court associated with the reservation.
     */
    public function court() {
        return $this->belongsTo(Court::class);
    }

    /**
     * Get the user associated with the reservation.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
    
}
