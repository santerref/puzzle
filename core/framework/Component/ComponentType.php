<?php

namespace Puzzle\Component;

use Illuminate\Support\Arr;

class ComponentType
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
        if (empty($info['settings'])) {
            $info['settings'] = [];
        }
        foreach ($info['settings']['fields'] ?? [] as &$field) {
            $field['value'] = '';
        }
        $info['id'] = $id;
        return new ComponentType($id, $path, $info, $version);
    }

    public function isRoot(): bool
    {
        if (!empty($this->info['root'])) {
            return filter_var($this->info['root'], FILTER_VALIDATE_BOOLEAN);
        }
        return false;
    }

    public function isHidden(): bool
    {
        if (!empty($this->info['hidden'])) {
            return filter_var($this->info['hidden'], FILTER_VALIDATE_BOOLEAN);
        }
        return false;
    }

    public function isContainer(): bool
    {
        if (!empty($this->info['container'])) {
            return filter_var($this->info['container'], FILTER_VALIDATE_BOOLEAN);
        }
        return false;
    }

    public function isPlaceholder(): bool
    {
        if (!empty($this->info['placeholder'])) {
            return filter_var($this->info['placeholder'], FILTER_VALIDATE_BOOLEAN);
        }
        return false;
    }

    public function hasFields(): bool
    {
        return !empty($this->info['settings']['fields']);
    }

    public function toArray(): array
    {
        return array_merge($this->info, [
            'container' => $this->isContainer(),
            'root' => $this->isRoot(),
            'hidden' => $this->isHidden(),
            'placeholder' => $this->isPlaceholder(),
            'has_fields' => $this->hasFields(),
        ]);
    }

    public function getSetting(string $key, mixed $defaultValue): mixed
    {
        return Arr::get($this->info['settings'], $key, $defaultValue);
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
