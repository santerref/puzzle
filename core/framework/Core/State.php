<?php

namespace Puzzle\Core;

use Puzzle\Exceptions\UnserializableValueException;
use Puzzle\Storage\Database;
use Symfony\Component\Serializer\Serializer;

class State
{
    public function __construct(protected Serializer $serializer)
    {
    }

    public function get(string $key, mixed $defaultValue = null): mixed
    {
        if (!Database::schema()->hasTable('key_value')) {
            return $defaultValue;
        }

        $keyValue = Database::table('key_value')->where('name', $key)->first();
        if (!empty($keyValue)) {
            return match ($keyValue->type) {
                'json' => $this->serializer->decode($keyValue->value, 'json'),
                default => $this->serializer->deserialize($keyValue->value, $keyValue->type, 'json')
            };
        }
        return $defaultValue;
    }

    public function set(string $key, mixed $value): void
    {
        Database::table('key_value')->updateOrInsert([
            'name' => $key
        ], [
            'value' => $this->serializer->serialize($value, 'json'),
            'type' => $this->getValueType($value)
        ]);
    }

    protected function getValueType(mixed $value): string
    {
        $type = gettype($value);
        if (in_array($type, ['resource', 'resource (closed)', 'NULL', 'unknown type'])) {
            throw new UnserializableValueException();
        }
        if ($type == 'object') {
            return get_class($value);
        }
        return 'json';
    }

    public function delete($key): bool
    {
        Database::table('key_value')->where('name', $key)->delete();
    }
}
