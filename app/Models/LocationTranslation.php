<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'locale',
        'title',
        'description',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
