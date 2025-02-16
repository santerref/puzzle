<?php

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Bootstrap;
use Puzzle\Core\Setup\Installer;
use Puzzle\Event\InstallerFinishedEvent;
use Puzzle\Storage\Database;

use function Symfony\Component\Clock\now;

require '../autoload.php';

$container = Bootstrap::boot();

if (!Database::schema()->hasTable('installer_scripts')) {
    Database::schema()->create('installer_scripts', function (Blueprint $table) {
        $table->string('class_name')->index()->unique();
    });
}

$state = $container->get('state');
if (!$state->get('installed_at')) {
    $installer = new Installer($container);
    $installer->run();
    $state->set('installed_at', now());

    $eventDispatcher = $container->get('event_dispatcher');
    $eventDispatcher->dispatch(new InstallerFinishedEvent($container), InstallerFinishedEvent::NAME);
}
