<?php

namespace Puzzle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class TestCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = Process::fromShellCommandline('php vendor/bin/phpunit && php vendor/bin/phpcs && npm run lint');
        return $process->run(function ($type, $buffer): void {
            echo $buffer;
        });
    }

    protected function configure()
    {
        $this->setName('test')
            ->setDescription('Run PHPUnit, PHPCS and eslint');
    }
}
