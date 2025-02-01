<?php

namespace Puzzle\file\Event;

use Puzzle\file\Entity\File;
use Symfony\Contracts\EventDispatcher\Event;

class PreInsertFileEvent extends Event
{
    public const NAME = 'file.pre_insert';

    public function __construct(private readonly File $file)
    {
    }

    public function getFile(): File
    {
        return $this->file;
    }
}
