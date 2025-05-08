<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'external_id',
        'head_id'
    ];

    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }



    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function isHeadMember(User $user): bool
    {
        return $this->head_id === $user->id;
    }


    public function applications()
    {
        return $this->hasManyThrough(
            Application::class, // Целевая модель
            User::class,        // Промежуточная модель
            'unit_id',          // Внешний ключ в промежуточной таблице (unit_user)
            'user_id',          // Внешний ключ в целевой таблице (applications)
            'id',               // Локальный ключ текущей модели (units.id)
            'id'                // Локальный ключ промежуточной модели (users.id)
        );
    }
}
