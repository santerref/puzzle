<?php

namespace Puzzle\Component;

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
        foreach ($info['fields'] as &$field) {
            $field['value'] = '';
        }
        $info['id'] = $id;
        return new Component($id, $path, $info);
    }

    public function updateFields($values)
    {
        foreach ($values as $field => $value) {
            $this->info['fields'][$field]['value'] = $value;
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
        return $this->id . '/' . $this->info['template'];
    }

    public function getArgs($value = false)
    {
        return array_map(
            function ($field) use ($value) {
                return $value ? $field['value'] : $field['default_value'];
            },
            $this->info['fields']
        ) + ['css' => $this->info['css'] ?? []];
    }
}
