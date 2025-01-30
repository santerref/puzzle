<?php

namespace Puzzle\page\Entity;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Puzzle\Entity\Entity;

class Component extends Entity
{
    use HasUuids;

    protected $fillable = [
        'component_type',
        'rendered_html',
        'form_values',
        'weight'
    ];

    protected $casts = [
        'form_values' => 'array'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
