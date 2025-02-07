<?php

namespace Puzzle\file\Twig;

use Puzzle\file\Entity\File;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('responsive_image', [$this, 'responsiveImage'], ['is_safe' => ['html']]),
        ];
    }

    public function responsiveImage($fileId): ?string
    {
        $file = File::find($fileId);
        if (!$file->is_image) {
            return null;
        }

        $originalWidth = $file->width;

        $breakpoints = [40, 48, 64, 80, 96];
        $responsiveImages = [];

        foreach ($breakpoints as $rem) {
            $maxWidth = $rem * 16;
            if ($originalWidth >= $maxWidth) {
                $url = "https://puzzle.ddev.site/admin/files/{$fileId}/{$maxWidth}/{$file->filename}";
                $responsiveImages[$maxWidth] = $url;
            }
        }

        $srcset = [];
        foreach ($responsiveImages as $width => $url) {
            $srcset[] = "{$url} {$width}w";
        }

        $src = 'https://puzzle.ddev.site/storage/' . $file->filename;
        $srcset = implode(', ', $srcset);
        $sizes = implode(', ', [
            '(min-width: 96rem) 96rem',
            '(min-width: 80rem) 80rem',
            '(min-width: 64rem) 64rem',
            '(min-width: 48rem) 48rem',
            '(min-width: 40rem) 40rem',
            '100vw'
        ]);
        $alt = $file->alt;

        return '<img src="' . $src . '" srcset="' . $srcset . '" sizes="' . $sizes . '" alt="' . $alt . '"/>';
    }
}
