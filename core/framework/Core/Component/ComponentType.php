<?php

namespace Puzzle\Core\Component;

use Illuminate\Support\Arr;
use Puzzle\Puzzle;

class ComponentType implements \JsonSerializable
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $version,
        protected string $template,
        protected array $assets = [],
        protected array $settings = [],
        protected array $fields = []
    ) {
        $this->settings = array_merge($this->getDefaultSettings(), $this->settings);
    }

    public static function createFromInfo(string $id, string $path, array $info, string $version): self
    {
        $assets = Arr::get($info, 'assets', []);
        $settings = Arr::get($info, 'settings', []);
        $fields = [];
        $weight = 0;
        foreach ($info['fields'] ?? [] as $key => &$field) {
            $fieldType = Puzzle::fieldType($field['type']);
            $fieldSettings = Arr::get($field, 'settings', []);
            $fieldType->validateSettings($fieldSettings);
            $fields[$key] = new Field(
                $key,
                $fieldType,
                $field['label'],
                $field['default_value'] ?? null,
                $weight++,
                $fieldSettings
            );
        }
        return new ComponentType($id, $info['name'], $version, $info['template'], $assets, $settings, $fields);
    }

    protected function getDefaultSettings(): array
    {
        return [
            'root' => false,
            'container' => false,
            'placeholder' => false,
            'hidden' => false,
            'live' => false,
            'positions' => [],
            'css' => [],
            'default_position' => null
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'version' => $this->version,
            'template' => $this->getTemplate(),
            'settings' => $this->settings,
            'fields' => array_values($this->fields)
        ];
    }

    public function getSetting(string $key, mixed $defaultValue): mixed
    {
        return Arr::get($this->settings, $key, $defaultValue);
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getFields(): array
    {
        return $this->fields;
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
        return $this->id . '/' . $this->template;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getStylesheets(): array
    {
        return Arr::get($this->assets, 'css', []);
    }

    public function getHeadScripts(): array
    {
        $headScripts = [];
        $scripts = Arr::get($this->assets, 'js', []);
        foreach ($scripts as $path => $attributes) {
            $head = Arr::get($attributes, 'head', false);
            if ($head) {
                $headScripts[$path] = Arr::except($attributes, ['head']);
            }
        }
        return $headScripts;
    }

    public function getFooterScripts(): array
    {
        $footerScripts = [];
        $scripts = Arr::get($this->assets, 'js', []);
        foreach ($scripts as $path => $attributes) {
            $head = Arr::get($attributes, 'head', false);
            if (!$head) {
                $footerScripts[$path] = Arr::except($attributes, ['head']);
            }
        }
        return $footerScripts;
    }
}
