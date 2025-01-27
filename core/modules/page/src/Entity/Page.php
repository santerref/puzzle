<?php

namespace Puzzle\page\Entity;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Puzzle\Entity\Entity;

class Page extends Entity
{
    use HasUuids;

    protected $with = [
        'components'
    ];

    protected $fillable = [
        'title'
    ];

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }
}
