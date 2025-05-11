<?php

namespace Puzzle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class TestCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $envFromXml = [];
        $xml = simplexml_load_file(getcwd() . '/phpunit.xml');
        foreach ($xml->php->env as $envVar) {
            $name = (string)$envVar['name'];
            $value = (string)$envVar['value'];
            $envFromXml[$name] = $value;
        }

        $process = Process::fromShellCommandline(
            'php vendor/bin/phpunit && php vendor/bin/phpcs && npm run lint',
            getcwd(),
            array_merge(getenv(), $envFromXml)
        );
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
