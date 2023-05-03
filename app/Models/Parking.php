<?php

namespace App\Models;

use App\Models\Zone;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parking extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = ['user_id', 'vehicle_id', 'zone_id', 'start_time', 'stop_time', 'total_price'];

    // protected $primaryKey = 'id';
 
    protected $casts = [
        'start_time' => 'datetime',
        'stop_time' => 'datetime',
    ];


    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('stop_time');
    }

    public function scopeStopped($query)
    {
        return $query->whereNotNull('stop_time');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    protected static function booted()
    {
        static::addGlobalScope('user', function (Builder $builder) {
            $builder->where('user_id', auth()->id());
        });
    }
}
