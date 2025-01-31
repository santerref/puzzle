<?php

namespace Puzzle\file\Entity;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Puzzle\Entity\Entity;

class File extends Entity
{
    use HasUuids;

    protected $fillable = [
        'filename',
        'filemime',
        'filesize',
        'path'
    ];
}
