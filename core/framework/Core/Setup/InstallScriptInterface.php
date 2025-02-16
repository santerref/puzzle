<?php

namespace Puzzle\Core\Setup;

interface InstallScriptInterface
{
    public function install(): void;

    public function getDependencies(): array;
}
