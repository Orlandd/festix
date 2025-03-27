<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class VenueImage extends Model
{
    /** @use HasFactory<\Database\Factories\VenueImageFactory> */
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    

    protected static function boot()
    {
        parent::boot();

        // Set UUID sebelum membuat model
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    protected $fillable = [
        'id',
        'venue_id',
        'name',
        'link'
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
}
