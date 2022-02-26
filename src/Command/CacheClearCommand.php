<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Command;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cache:clear',
    description: 'Add a short description for your command',
)]
class CacheClearCommand extends Command
{
    protected function configure(): void
    {
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $cache = new FilesystemAdapter();
        $cache->clear();

        $io->success('Cache cleared!');

        return Command::SUCCESS;
    }
}
