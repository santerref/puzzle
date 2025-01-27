<?php

namespace Puzzle\Setup;

interface InstallScriptInterface
{
    public function install(): void;

    public function getDependencies(): array;
}
