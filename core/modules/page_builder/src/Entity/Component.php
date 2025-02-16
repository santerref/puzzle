<?php

namespace Puzzle\page_builder\Entity;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Puzzle\Storage\Entity\Entity;
use Puzzle\page\Entity\Page;

class Component extends Entity
{
    protected $fillable = [
        'component_type',
        'rendered_html',
        'form_values',
        'id',
        'position',
        'locked',
        'parent',
        'weight'
    ];

    protected $casts = [
        'form_values' => 'array',
        'is_new' => 'boolean'
    ];

    protected $appends = [
        'is_new'
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

    public function isNew(): Attribute
    {
        return Attribute::make(
            get: fn () => !$this->exists,
        );
    }
}
