<?php

namespace App\Command;

use App\Message\DownloadPostalCodes;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsCommand(
    name: 'app:download-postal-codes',
    description: 'Download postal codes to database',
)]
class DownloadPostalCodesCommand extends Command
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->setDescription("Download postal codes to database")
            ->setHelp("This command download postal codes from csv");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $envelope = new Envelope(new DownloadPostalCodes(), [
            new DelayStamp(500)
        ]);

        $this->messageBus->dispatch($envelope);

        return Command::SUCCESS;
    }
}
