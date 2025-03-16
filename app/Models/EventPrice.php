<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class EventPrice extends Model
{
    /** @use HasFactory<\Database\Factories\EventPriceFactory> */
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'event_id', 'total_seat', 'price', 'seat_category_id'];

    protected static function boot()
    {
        parent::boot();

        // Set UUID sebelum membuat model
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function seatCategory()
    {
        return $this->belongsTo(SeatCategory::class);
    }
}
