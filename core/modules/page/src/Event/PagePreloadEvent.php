<?php

namespace Puzzle\page\Event;

use Puzzle\Core\Asset\Link;
use Puzzle\Core\Asset\Script;
use Puzzle\Core\Asset\Stylesheet;
use Symfony\Contracts\EventDispatcher\Event;

class PagePreloadEvent extends Event
{
    public const string NAME = 'page.preload';

    protected array $cssVariables = [];

    protected array $stylesheets = [];

    protected array $headScripts = [];

    protected array $footerScripts = [];

    protected array $links = [];

    public function __construct(protected readonly array $components = [])
    {
    }

    public function addStylesheet(Stylesheet $stylesheet): void
    {
        $this->stylesheets[] = $stylesheet;
    }

    public function addHeadScript(Script $script): void
    {
        $this->headScripts[] = $script;
    }

    public function addFooterScript(Script $script): void
    {
        $this->footerScripts[] = $script;
    }

    public function addLink(Link $link): void
    {
        $this->links[] = $link;
    }

    public function getStylesheets(): array
    {
        return $this->stylesheets;
    }

    public function getHeadScripts(): array
    {
        return $this->headScripts;
    }

    public function getFooterScripts(): array
    {
        return $this->footerScripts;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function addCssVariables(array $variables): void
    {
        $this->cssVariables = array_merge($this->cssVariables, $variables);
    }

    public function setCssVariable(string $key, string $value): void
    {
        $this->cssVariables[$key] = $value;
    }

    public function getCssVariables(): array
    {
        return $this->cssVariables;
    }

    public function getComponents(): array
    {
        return $this->components;
    }
}
