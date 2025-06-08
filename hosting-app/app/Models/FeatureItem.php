<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureItem extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
    ];

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function applications()
    {
        return $this->belongsToMany(Application::class, 'application_feature_item');
    }
}
