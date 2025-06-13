<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'unit_id',
        'head_id',
        'notes',
        'responsible_id',
        'status',
        'approved',
        'approved_at',
        'domain',
    ];

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function head()
    {
        return $this->belongsTo(Head::class);
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
            'completed' => 'Завершено',
        ][$this->status];
    }
}
