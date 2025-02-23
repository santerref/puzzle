<?php

namespace Puzzle\page_builder\Entity;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Puzzle\page\Entity\Page;
use Puzzle\Puzzle;
use Puzzle\Storage\Entity\Entity;

class Component extends Entity
{
    protected $fillable = [
        'component_type',
        'id',
        'position',
        'locked',
        'parent',
        'weight'
    ];

    protected $casts = [
        'is_new' => 'boolean',
        'locked' => 'boolean'
    ];

    protected $appends = [
        'is_new',
        'rendered_html'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $with = [
        'children',
        'componentFields'
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Component::class, 'parent')->orderBy('weight');
    }

    public function componentFields(): HasMany
    {
        return $this->hasMany(ComponentField::class)->orderBy('weight');
    }

    public function isNew(): Attribute
    {
        return Attribute::make(
            get: fn() => !$this->exists,
        );
    }

    public function renderedHtml(): Attribute
    {
        return Attribute::make(
            get: function () {
                $renderer = Puzzle::service('component.renderer');
                $componentDiscovery = Puzzle::service('component_discovery');

                if (!empty($this->component_type)) {
                    $componentType = $componentDiscovery->get($this->component_type);
                    return $renderer->render($componentType, $this);
                }

                return '';
            }
        );
    }

    public function toTemplateArgs(): array
    {
        $args = [
            'id' => $this->id,
            'component_type' => $this->component_type,
            'position' => $this->position,
            'locked' => $this->locked,
            'parent' => $this->parent,
            'weight' => $this->weight,
            'children' => $this->children
        ];

        $fields = [];
        foreach ($this->componentFields as $componentField) {
            $fields[$componentField->field_name] = $componentField->value;
        }
        $args['fields'] = $fields;

        return $args;
    }
}
