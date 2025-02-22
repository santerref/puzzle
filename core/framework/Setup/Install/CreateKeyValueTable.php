<?php

namespace Puzzle\Setup\Install;

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Core\Setup\InstallScriptInterface;
use Puzzle\Storage\Database;

class CreateKeyValueTable implements InstallScriptInterface
{
    public function install(): void
    {
        Database::schema()->create('key_value', function (Blueprint $table) {
            $table->string('name')->unique();
            $table->string('type');
            $table->binary('value');
        });
        Database::statement('ALTER TABLE key_value MODIFY value LONGBLOB');
    }

    public function getDependencies(): array
    {
        return [];
    }
}
