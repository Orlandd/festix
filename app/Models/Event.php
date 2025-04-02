<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'date',
        'time',
        'description',
        'venue_id',
    ];

    protected static function boot()
    {
        parent::boot();

        // Set UUID sebelum membuat model
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function vanue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function eventPrice()
    {
        return $this->hasMany(EventPrice::class);
    }

    public function eventImage()
    {
        return $this->hasMany(EventImage::class);
    }
}
