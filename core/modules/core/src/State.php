<?php

namespace Puzzle\core;

use Puzzle\core\Entity\KeyValue;
use Puzzle\Exception\UnserializableValueException;
use Puzzle\Storage\Database;
use Symfony\Component\Serializer\Serializer;

class State
{
    public function __construct(protected Serializer $serializer)
    {
    }

    public function get(string $key, mixed $defaultValue = null): mixed
    {
        if (!Database::table('key_value')->exists()) {
            return $defaultValue;
        }

        $keyValue = KeyValue::where('name', $key)->first();
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
        KeyValue::updateOrCreate([
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
        KeyValue::where('name', $key)->delete();
    }
}
