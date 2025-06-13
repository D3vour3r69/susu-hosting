<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
    ];

    public function items()
    {
        return $this->hasMany(FeatureItem::class);
    }
}
