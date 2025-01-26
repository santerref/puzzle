<?php

namespace Puzzle\Storage;

use Puzzle\Model\Model;

interface StorageInterface
{
    public function save(Model $model): bool;
}
