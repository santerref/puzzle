<?php

namespace Puzzle\component\Entity;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Puzzle\Entity\Entity;
use Puzzle\page\Entity\Page;

class Component extends Entity
{
    use HasUuids;

    protected $fillable = [
        'component_type',
        'rendered_html',
        'form_values',
        'weight',
        'parent',
        'container',
        'id',
        'position'
    ];

    protected $casts = [
        'form_values' => 'array'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $with = [
        'children'
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Component::class, 'parent')->orderBy('weight');
    }
}
