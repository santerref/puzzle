<?php

namespace Puzzle\Storage;

use Puzzle\Storage\Entity\Model;

interface StorageInterface
{
    public function save(Model $model): bool;
}
