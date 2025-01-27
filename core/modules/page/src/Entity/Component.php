<?php

namespace Puzzle\page\Entity;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Puzzle\Entity\Entity;

class Component extends Entity
{
    protected $fillable = [
        'component_type',
        'content'
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
