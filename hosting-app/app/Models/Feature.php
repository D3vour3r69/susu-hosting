<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;
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
