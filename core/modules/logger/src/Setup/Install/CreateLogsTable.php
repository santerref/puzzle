<?php

namespace Puzzle\logger\Setup\Install;

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Core\Setup\InstallScriptInterface;
use Puzzle\Storage\Database;

class CreateLogsTable implements InstallScriptInterface
{
    public function install(): void
    {
        Database::schema()->create('logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('channel');
            $table->integer('level');
            $table->string('level_name');
            $table->string('datetime')->nullable();
            $table->text('message')->nullable();
            $table->longText('context')->nullable();
            $table->text('extra')->nullable();
            $table->timestamps();
        });
    }

    public function getDependencies(): array
    {
        return [];
    }
}
