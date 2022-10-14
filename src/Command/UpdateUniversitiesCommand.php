<?php

namespace App\Command;

use App\Message\UpdateUniversities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsCommand(
    name: 'app:update-universities',
    description: 'Update Universitites databaase',
)]
class UpdateUniversitiesCommand extends Command
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription("Update universities list")
            ->setHelp("This command able you to update universities list");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $envelope = new Envelope(new UpdateUniversities("https://polon.nauka.gov.pl/opi-ws/api/academicInstitutions"), [
            new DelayStamp(500)
        ]);

        $this->messageBus->dispatch($envelope);

        return 1;
    }
}
