<?php

namespace Puzzle\page_builder\Setup\Install;

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Core\Setup\InstallScriptInterface;
use Puzzle\Storage\Database;

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
            $table->foreignUuid('parent')->nullable()->default(null)
                ->references('id')->on('components')
                ->cascadeOnDelete();
            $table->unsignedInteger('weight');
            $table->string('position')->nullable();
            $table->boolean('locked')->default(false);
            $table->timestamps();
        });
    }

    public function getDependencies(): array
    {
        return [];
    }
}
