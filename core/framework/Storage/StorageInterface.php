<?php

namespace Puzzle\Storage;

use Puzzle\Entity\Model;

interface StorageInterface
{
    public function save(Model $model): bool;
}
