<?php

namespace Puzzle\file\Entity;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Puzzle\Storage\Entity\Entity;

class File extends Entity
{
    protected $fillable = [
        'filename',
        'filemime',
        'filesize',
        'path',
        'is_image',
        'width',
        'height',
        'focal_point_x',
        'focal_point_y',
        'title',
        'alt'
    ];
}
