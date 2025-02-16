<?php

namespace Puzzle\Storage\Entity;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

abstract class Entity extends Model
{
    use HasUuids;
}
