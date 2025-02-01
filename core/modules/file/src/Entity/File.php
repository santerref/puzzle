<?php

namespace Puzzle\file\Entity;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Puzzle\Entity\Entity;

class File extends Entity
{
    protected $fillable = [
        'filename',
        'filemime',
        'filesize',
        'path'
    ];
}
