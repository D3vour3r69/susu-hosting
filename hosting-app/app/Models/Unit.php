<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'head_name',
        'external_id'
    ];

    protected $casts = [
        'external_id' => 'string' // или 'string' в зависимости от того что в API получается.
    ];

    public function externalEntity()
    {
        // Пример связи через API-шлюз
        return ExternalApiService::getById($this->external_id);
    }
}
