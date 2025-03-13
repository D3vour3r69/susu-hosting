<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    public function items()
    {
        return $this->hasMany(FeatureItem::class);
    }
}
