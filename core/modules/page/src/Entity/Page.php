<?php

namespace Puzzle\page\Entity;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Puzzle\page_builder\Entity\Component;
use Puzzle\Entity\Entity;

class Page extends Entity
{
    protected $fillable = [
        'title',
        'slug'
    ];

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }
}
