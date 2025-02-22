<?php

namespace Puzzle\page_builder\Entity;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Puzzle\Enums\FieldValueType;
use Puzzle\Storage\Entity\Entity;

class ComponentField extends Entity
{
    protected $fillable = [
        'id',
        'field_name',
        'field_type',
        'value_type',
        'int_value',
        'varchar_value',
        'text_value',
        'json_value',
        'blob_value',
        'weight',
    ];

    protected $casts = [
        'field_name' => 'string',
        'field_type' => 'string',
        'value_type' => FieldValueType::class,
        'int_value' => 'integer',
        'varchar_value' => 'string',
        'text_value' => 'string',
        'json_value' => 'array',
        'bool_value' => 'boolean',
        'weight' => 'integer',
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    public function value(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->{$this->value_type->value . '_value'}
        );
    }
}
