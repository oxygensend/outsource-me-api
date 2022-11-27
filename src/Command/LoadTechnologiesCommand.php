<?php

namespace App\Command;

use App\Message\UpdateDevelopersOrder;
use App\Message\UpdateJobOffersOrder;
use App\Parser\TechnologyParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsCommand(
    name: 'app:load-technologies',
    description: 'Load technologies from file to database',
)]
class LoadTechnologiesCommand  extends Command
{
    public function __construct(readonly private TechnologyParser $parser)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->setDescription("Load technologies from file to database")
            ->setHelp("This command will load technologies into database");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->parser->parse();

        return Command::SUCCESS;
    }
}