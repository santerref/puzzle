<?php

namespace Puzzle\component\Setup\Install;

use Illuminate\Database\Schema\Blueprint;
use Puzzle\page\Setup\Install\CreatePagesTable;
use Puzzle\Storage\Database;
use Puzzle\Setup\InstallScriptInterface;

class CreateComponentsTable implements InstallScriptInterface
{
    public function install(): void
    {
        Database::schema()->create('components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('page_id')->references('id')->on('pages')->cascadeOnDelete();
            $table->string('component_type');
            $table->binary('rendered_html');
            $table->binary('form_values');
            $table->integer('weight');
            $table->boolean('container');
            $table->foreignUuid('parent')->nullable()->default(null)
                ->references('id')->on('components')
                ->cascadeOnDelete();
            $table->string('position')->nullable();
            $table->timestamps();
        });
    }

    public function getDependencies(): array
    {
        return [CreatePagesTable::class];
    }
}
