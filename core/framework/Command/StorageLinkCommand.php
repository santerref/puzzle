<?php

namespace Puzzle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class StorageLinkCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();

        $target = '../storage/public';
        $link = 'public/storage';

        if (!is_link($link)) {
            $filesystem->symlink($target, $link);
        }

        return 0;
    }

    protected function configure()
    {
        $this->setName('storage:link')
            ->setDescription('Create symlink for public files');
    }
}
