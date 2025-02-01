<?php

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Bootstrap;
use Puzzle\Event\InstallerFinishedEvent;
use Puzzle\Setup\Installer;
use Puzzle\Storage\Database;

require '../autoload.php';

$container = Bootstrap::boot();

if (!Database::schema()->hasTable('installer_scripts')) {
    Database::schema()->create('installer_scripts', function (Blueprint $table) {
        $table->string('class_name')->index()->unique();
    });
}

$eventDispatcher = $container->get('event_dispatcher');
$installer = new Installer($eventDispatcher);
$installer->run();

$eventDispatcher->dispatch(new InstallerFinishedEvent($container), InstallerFinishedEvent::NAME);
