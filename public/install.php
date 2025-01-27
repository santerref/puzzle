<?php

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Bootstrap;
use Puzzle\Setup\Installer;
use Puzzle\Storage\Database;

require '../vendor/autoload.php';

$container = Bootstrap::boot();

if (!Database::schema()->hasTable('installer_scripts')) {
    Database::schema()->create('installer_scripts', function (Blueprint $table) {
        $table->string('class_name')->index()->unique();
    });
}

$installer = new Installer($container->get('event_dispatcher'));
$installer->run();
