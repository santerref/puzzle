<?php

namespace Puzzle\page\Setup\Install;

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Core\Setup\InstallScriptInterface;
use Puzzle\Storage\Database;

class CreatePagesTable implements InstallScriptInterface
{
    public function install(): void
    {
        Database::schema()->create('pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug');
            $table->foreignUuid('parent')->nullable()->default(null)
                ->references('id')->on('pages')
                ->nullOnDelete();
            $table->unsignedInteger('weight')->default(0);
            $table->timestamps();
        });
    }

    public function getDependencies(): array
    {
        return [];
    }
}
