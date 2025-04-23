<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'notes',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d.m.Y H:i');
    }

    public function featureItems()
    {
        return $this->belongsToMany(FeatureItem::class, 'application_feature_item');
    }

    public function getStatusTextAttribute()
    {
        return [
            'active' => 'Активно',
            'inactive' => 'Не активно',
            'completed' => 'Завершено'
        ][$this->status];
    }
}
