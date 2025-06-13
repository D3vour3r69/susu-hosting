<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Head extends Model
{
    protected $fillable = [
        'full_name',
        'position',
        'unit_id',
        'email',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getAddressTitleAttribute()
    {
        // Заменяем "Начальник" на "Начальнику" и т.д.
        return str_replace(
            ['Начальник', 'Проректор', 'Руководитель'],
            ['Начальнику', 'Проректору', 'Руководителю'],
            $this->position
        );
    }

    public function getFullNameAttribute()
    {
        $originalFullName = $this->attributes['full_name'] ?? '';
        return str_replace(
            ['Подивилова Елена Олеговна', 'Латухин Дмитрий Викторович', 'Кабиольский Евгений Алексеевич'],
            ['Подивиловой Елене Олеговне', 'Латухину Дмитрию Викторовичу', 'Кабиольскому Евгению Алексеевичу'],
            $originalFullName
        );
    }
}
