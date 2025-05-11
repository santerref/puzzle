<?php

namespace Puzzle\page\Event;

use Puzzle\Core\Asset\Link;
use Puzzle\Core\Asset\Script;
use Puzzle\Core\Asset\Stylesheet;
use Symfony\Contracts\EventDispatcher\Event;

class AssetsEvent extends Event
{
    public const string NAME = 'page.assets';

    public const string HEAD = 'head';

    public const string FOOTER = 'footer';

    protected array $stylesheets = [];

    protected array $scripts = [];

    protected array $links = [];

    public function __construct(protected string $location)
    {
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function addStylesheet(Stylesheet $stylesheet): void
    {
        $this->stylesheets[] = $stylesheet;
    }

    public function addScript(Script $script): void
    {
        $this->scripts[] = $script;
    }

    public function addLink(Link $link): void
    {
        $this->links[] = $link;
    }

    public function getStylesheets(): array
    {
        return $this->stylesheets;
    }

    public function getScripts(): array
    {
        return $this->scripts;
    }

    public function getLinks(): array
    {
        return $this->links;
    }
}
