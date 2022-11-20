<?php

namespace App\Command;

use App\Message\UpdateDevelopersOrder;
use App\Message\UpdateJobOffersOrder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsCommand(
    name: 'app:update-offers-order',
    description: 'Add a short description for your command',
)]
class UpdateOffersOrderCommand extends Command
{
    public function __construct(readonly private MessageBusInterface $messageBus)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->setDescription("Update popularity order of offer(developers/job offers) every day")
            ->setHelp("This command update popularity rate of offers");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $envelopeJobOffers = new Envelope(new UpdateJobOffersOrder(), [
            new DelayStamp(1000)
        ]);

        $envelopeDevelopers = new Envelope(new UpdateDevelopersOrder(), [
            new DelayStamp(1000)
        ]);

        $this->messageBus->dispatch($envelopeJobOffers);
        $this->messageBus->dispatch($envelopeDevelopers);

        return Command::SUCCESS;
    }
}
