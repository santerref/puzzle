<?php

namespace Puzzle\core\Entity;

use Puzzle\Storage\Entity\Entity;

class KeyValue extends Entity
{
    protected $table = 'key_value';

    protected $fillable = [
        'name',
        'value',
        'type'
    ];

    public $timestamps = false;
}
