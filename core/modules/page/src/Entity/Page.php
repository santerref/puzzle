<?php

namespace Puzzle\page\Entity;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Puzzle\page_builder\Entity\Component;
use Puzzle\Storage\Entity\Entity;

class Page extends Entity
{
    protected $fillable = [
        'title',
        'slug',
        'parent',
    ];

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent')->orderBy('weight');
    }
}
