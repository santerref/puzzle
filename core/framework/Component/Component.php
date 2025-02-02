<?php

namespace Puzzle\Component;

use Illuminate\Support\Arr;

class Component
{
    public function __construct(
        protected string $id,
        protected string $path,
        protected array $info,
        protected string $version
    ) {
    }

    public static function createFromInfo(string $id, string $path, array $info, string $version): self
    {
        foreach ($info['settings']['fields'] ?? [] as &$field) {
            $field['value'] = '';
        }
        $info['id'] = $id;
        return new Component($id, $path, $info, $version);
    }

    public function toArray(): array
    {
        return $this->info;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getType(): string
    {
        return $this->id;
    }

    public function getTemplate(): string
    {
        return $this->id . '/' . $this->info['settings']['template'];
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

    public function getDefaultValues(): array
    {
        $values = [];
        foreach (Arr::get($this->info, 'settings.fields', []) as $key => $field) {
            $values[$key] = Arr::get($field, 'default_value');
        }
        return $values;
    }
}
