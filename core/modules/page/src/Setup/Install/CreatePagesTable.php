<?php

namespace Puzzle\page\Setup\Install;

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Storage\Database;
use Puzzle\Setup\InstallScriptInterface;

class CreatePagesTable implements InstallScriptInterface
{
    public function install(): void
    {
        Database::schema()->create('pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->timestamps();
        });
    }

    public function getDependencies(): array
    {
        return [];
    }
}
