<?php

namespace Puzzle\page\Entity;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Puzzle\Entity\Entity;

class Page extends Entity
{
    use HasUuids;

    protected $fillable = [
        'title'
    ];
}
