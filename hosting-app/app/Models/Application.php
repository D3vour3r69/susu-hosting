<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function featureItems()
    {
        return $this->belongsToMany(FeatureItem::class);
    }
}
