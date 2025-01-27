<?php

namespace Puzzle\Model;

use Illuminate\Support\Str;
use Puzzle\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class Model
{
    protected static StorageInterface $storage;

    protected ParameterBag $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = new ParameterBag($attributes);
    }

    public static function setStorage(StorageInterface $storage): void
    {
        static::$storage = $storage;
    }

    public function toArray(): array
    {
        return $this->attributes->all();
    }

    public function save(): bool
    {
        return static::$storage->save($this);
    }

    public function setAttribute(string $key, mixed $value): self
    {
        $this->attributes->set($key, $value);
        return $this;
    }

    public function has($key): bool
    {
        return $this->attributes->has($key);
    }

    public function getAttribute($key, $defaultValue = null): mixed
    {
        return $this->attributes->get($key, $defaultValue);
    }

    public function tableName()
    {
        $reflect = new \ReflectionClass($this);
        $className = $reflect->getShortName();

        return Str::plural(Str::snake($className));
    }
}
