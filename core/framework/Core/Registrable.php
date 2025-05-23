<?php

namespace Puzzle\Core;

use Illuminate\Support\Arr;
use Puzzle\Core\Validator\BaseValidator;
use Puzzle\Core\Validator\DefinitionValidatorInterface;
use Puzzle\Exceptions\InvalidDefinitionException;

abstract class Registrable
{
    public function __construct(
        protected string $name,
        protected \SplFileInfo $file,
        protected array $definition
    ) {
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDefinition(): array
    {
        return $this->definition;
    }

    public function getPath(): string
    {
        return $this->file->getPath();
    }

    public function getNamespace(): string
    {
        return 'Puzzle\\' . $this->getName();
    }

    public function validate(): void
    {
        try {
            $this->getValidator()->validate($this->definition);
        } catch (\Throwable $e) {
            throw new InvalidDefinitionException(
                sprintf('Invalid definition in "%s": %s', $this->file->getRealPath(), $e->getMessage()),
            );
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->definition, $key, $default);
    }

    public function has(string $key): bool
    {
        return Arr::has($this->definition, $key);
    }

    protected function getValidator(): DefinitionValidatorInterface
    {
        return new BaseValidator();
    }
}
