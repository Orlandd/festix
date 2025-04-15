<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EventSeat extends Model
{
    /** @use HasFactory<\Database\Factories\EventSeatFactory> */
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['seat_category_id', 'venue_seat_id', 'event_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function seatCategory()
    {
        return $this->belongsTo(SeatCategory::class, 'seat_category_id');
    }

    public function venueSeat()
    {
        return $this->belongsTo(VenueSeat::class, 'venue_seat_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // -------------------------------Lama-----------------------------------
    // use HasFactory;

    // public $incrementing = false;
    // protected $keyType = 'string';

    // protected static function boot()
    // {
    //     parent::boot();

    //     // Set UUID sebelum membuat model
    //     static::creating(function ($model) {
    //         $model->id = Str::uuid();
    //     });
    // }
}
