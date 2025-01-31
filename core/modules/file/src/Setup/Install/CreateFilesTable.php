<?php

namespace Puzzle\file\Setup\Install;

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Setup\InstallScriptInterface;
use Puzzle\Storage\Database;

class CreateFilesTable implements InstallScriptInterface
{
    public function install(): void
    {
        Database::schema()->create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('filename');
            $table->string('filemime');
            $table->unsignedInteger('filesize');
            $table->string('path');
            $table->boolean('permanent')->default(false);
            $table->timestamps();
        });
    }

    public function getDependencies(): array
    {
        return [];
    }
}
