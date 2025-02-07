<?php

namespace Puzzle\file\Controller;

use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use Puzzle\file\Entity\File;
use Puzzle\file\Event\PreInsertFileEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController
{
    public function __construct(private EventDispatcher $eventDispatcher)
    {
    }

    public function upload(Request $request): JsonResponse
    {
        $uploadFile = $request->files->get('upload_file');
        $filemime = $uploadFile->getMimeType();
        $filename = $uploadFile->getClientOriginalName();
        $filesize = $uploadFile->getSize();

        $file = new File([
            'filename' => $filename,
            'filemime' => $filemime,
            'filesize' => $filesize,
            'permanent' => false,
        ]);

        $this->eventDispatcher->dispatch(new PreInsertFileEvent($file), PreInsertFileEvent::NAME);
        $filename = $file->getAttribute('filename');
        $file->setAttribute('path', '/storage/public/' . $filename);
        $uploadFile->move(PUZZLE_ROOT . '/storage/public', $filename);

        $imageInfo = getimagesize(PUZZLE_ROOT . $file->path);
        if ($imageInfo !== false) {
            $file->setAttribute('is_image', true);
            $file->setAttribute('width', $imageInfo[0]);
            $file->setAttribute('height', $imageInfo[1]);
        }
        $file->save();

        return new JsonResponse($file);
    }

    public function save(File $file, Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $file->setAttribute('permanent', true);
        $file->setAttribute('title', !empty($payload['title']) ? $payload['title'] : null);
        $file->setAttribute('alt', !empty($payload['alt']) ? $payload['alt'] : null);

        if (
            !empty($payload['image']['focal_point_x']) && $payload['image']['focal_point_x'] >= 0
            && !empty($payload['image']['focal_point_y']) && $payload['image']['focal_point_y'] >= 0
        ) {
            $file->setAttribute('focal_point_x', $payload['image']['focal_point_x']);
            $file->setAttribute('focal_point_y', $payload['image']['focal_point_y']);
        }
        $file->save();

        return new JsonResponse($file);
    }

    public function index(): JsonResponse
    {
        return new JsonResponse(File::orderBy('created_at', 'DESC')->limit(20)->get());
    }

    public function size(File $file, string $size, string $filename): StreamedResponse
    {
        $server = ServerFactory::create([
            'source' => PUZZLE_ROOT . '/storage/public',
            'cache' => PUZZLE_ROOT . '/storage/public/sizes/' . $size,
            'response' => new SymfonyResponseFactory()
        ]);

        [$width, $height] = explode('x', $size);
        return $server->getImageResponse($file->filename, [
            'w' => $width,
            'h' => $height,
            'q' => 100,
            'fit' => 'crop-' . $file->focal_point_x . '-' . $file->focal_point_y
        ]);
    }
}
