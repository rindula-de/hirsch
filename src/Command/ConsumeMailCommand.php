<?php

namespace App\Command;

use App\Message\SyncMenuMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:consume:mail',
    description: 'Add a short description for your command',
)]
class ConsumeMailCommand extends Command
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('delete', 'd', InputOption::VALUE_NONE, 'Delete all messages');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $delete = $input->getOption('delete');

        $this->messageBus->dispatch(new SyncMenuMessage(!!$delete));

        return Command::SUCCESS;
    }
}
