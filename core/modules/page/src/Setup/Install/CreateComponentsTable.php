<?php

namespace Puzzle\page\Setup\Install;

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Storage\Database;
use Puzzle\Setup\InstallScriptInterface;

class CreateComponentsTable implements InstallScriptInterface
{
    public function install(): void
    {
        Database::schema()->create('components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('page_id')->references('id')->on('pages');
            $table->string('component_type');
            $table->binary('content');
            $table->timestamps();
        });
    }

    public function getDependencies(): array
    {
        return [CreatePagesTable::class];
    }
}
