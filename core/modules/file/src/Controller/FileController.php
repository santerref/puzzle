<?php

namespace Puzzle\file\Controller;

use Puzzle\file\Entity\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FileController
{
    public function uploadImage(Request $request): JsonResponse
    {
        $image = $request->files->get('image');
        $filemime = $image->getMimeType();
        $filename = $image->getClientOriginalName();
        $filesize = $image->getSize();
        $path = '/storage/' . $filename;
        $image->move(PUZZLE_ROOT . '/storage/public', $filename);
        $file = File::create([
            'filename' => $filename,
            'filemime' => $filemime,
            'filesize' => $filesize,
            'path' => $path
        ]);

        return new JsonResponse($file);
    }
}
