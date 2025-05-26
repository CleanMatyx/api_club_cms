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
     * Get the user associated with the member.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
