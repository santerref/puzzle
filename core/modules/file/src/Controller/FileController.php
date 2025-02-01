<?php

namespace Puzzle\file\Controller;

use Puzzle\file\Entity\File;
use Puzzle\file\Event\PreInsertFileEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FileController
{
    public function __construct(private EventDispatcher $eventDispatcher)
    {
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $image = $request->files->get('image');
        $filemime = $image->getMimeType();
        $filename = $image->getClientOriginalName();
        $filesize = $image->getSize();

        $file = new File([
            'filename' => $filename,
            'filemime' => $filemime,
            'filesize' => $filesize,
            'permanent' => false,
        ]);

        $this->eventDispatcher->dispatch(new PreInsertFileEvent($file), PreInsertFileEvent::NAME);
        $filename = $file->getAttribute('filename');
        $file->setAttribute('path', '/storage/' . $filename);
        $image->move(PUZZLE_ROOT . '/storage/public', $filename);
        $file->save();

        return new JsonResponse($file);
    }
}
