<?php

namespace Puzzle\logger\Entity;

use Puzzle\Entity\Entity;

class Log extends Entity
{
    protected $fillable = [
        'channel',
        'level',
        'level_name',
        'datetime',
        'message',
        'context',
        'extra'
    ];

    protected $casts = [
        'extra' => 'array',
        'context' => 'array'
    ];
}
