<?php

namespace Puzzle\user\Setup\Install;

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Storage\Database;
use Puzzle\Core\Setup\InstallScriptInterface;

class CreateUsersTable implements InstallScriptInterface
{
    public function install(): void
    {
        Database::schema()->create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->text('password');
            $table->timestamps();
        });
    }

    public function getDependencies(): array
    {
        return [];
    }
}
