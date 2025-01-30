<?php

namespace Puzzle\Component;

use Illuminate\Support\Arr;

class Component
{
    public function __construct(
        protected string $id,
        protected string $path,
        protected array $info
    ) {
    }

    public static function createFromInfo(string $id, string $path, array $info)
    {
        foreach ($info['settings']['fields'] as &$field) {
            $field['value'] = '';
        }
        $info['id'] = $id;
        return new Component($id, $path, $info);
    }

    public function updateFields($values)
    {
        foreach ($values as $field => $value) {
            $this->info['settings']['fields'][$field]['value'] = $value;
        }
    }

    public function toArray()
    {
        return $this->info;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getTemplate()
    {
        return $this->id . '/versions/' . $this->version() . '/' . $this->info['settings']['template'];
    }

    public function getInfo(): array
    {
        return $this->info;
    }

    public function setInfo(array $info): void
    {
        $this->info = $info;
    }

    public function version(): string
    {
        return $this->info['version'];
    }

    public function getArgs($value = false)
    {
        return array_map(
            function ($field) use ($value) {
                return $value ? $field['value'] : $field['default_value'];
            },
            $this->info['settings']['fields']
        ) + ['css' => $this->info['settings']['css'] ?? []];
    }

    public function getDefaultValues(): array
    {
        $defaultValues = [];
        foreach (Arr::get($this->info, 'settings.fields', []) as $key => $field) {
            $defaultValues[$key] = Arr::get($field, 'default_value');
        }
        return $defaultValues;
    }
}
