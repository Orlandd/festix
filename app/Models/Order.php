<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['date', 'total', 'status', 'user_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    // --------------------------------------Lama--------------------------------------
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
