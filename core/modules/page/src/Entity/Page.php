<?php

namespace Puzzle\page\Entity;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Puzzle\component\Entity\Component;
use Puzzle\Entity\Entity;

class Page extends Entity
{
    use HasUuids;

    protected $with = [
        'components'
    ];

    protected $fillable = [
        'title',
        'slug'
    ];

    public function components(): HasMany
    {
        return $this->hasMany(Component::class)->orderBy('weight');
    }
}
