<?php

namespace Puzzle\user\Entity;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Puzzle\Entity\Entity;

class User extends Entity
{
    protected $fillable = [
        'email',
        'password'
    ];

    protected $hidden = [
        'password'
    ];
}
