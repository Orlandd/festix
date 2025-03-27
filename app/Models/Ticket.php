<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    // Buatan Billie 15/03/2025
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    // Buatan Billie 15/03/2025
    protected $fillable = [
        'code', 'status', 'seat_number', 'purchased_at',
        'payment_id', 'user_id', 'event_price_id'
    ];
    //-------------------------------------
    
    protected static function boot()
    {
        parent::boot();

        // Set UUID sebelum membuat model
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function eventPrice()
    {
        return $this->belongsTo(EventPrice::class, 'event_price_id', 'id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
