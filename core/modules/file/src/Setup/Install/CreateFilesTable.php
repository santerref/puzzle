<?php

namespace Puzzle\file\Setup\Install;

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Core\Setup\InstallScriptInterface;
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
            $table->string('title')->nullable();
            $table->string('alt')->nullable();
            $table->boolean('permanent')->default(false);
            $table->boolean('is_image')->default(false);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('focal_point_x')->default(50);
            $table->unsignedInteger('focal_point_y')->default(50);
            $table->timestamps();
        });
    }

    public function getDependencies(): array
    {
        return [];
    }
}
