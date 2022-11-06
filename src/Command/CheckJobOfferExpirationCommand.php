<?php

namespace App\Command;

use App\Entity\JobOffer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:check-job-offer-expiration',
    description: 'Add a short description for your command',
)]
class CheckJobOfferExpirationCommand extends Command
{
    public function __construct(readonly private EntityManagerInterface $em)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {

        $this
            ->setDescription("Check expiration date of job offer and archive expired ones")
            ->setHelp("This command controlls job offers");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jobOffers = $this->em->getRepository(JobOffer::class)->findExpiredJobOffers();

        /** @var JobOffer $jobOffer */
        foreach ($jobOffers as $jobOffer) {
            $jobOffer->setArchived(true);
        }

        $this->em->flush();


        return Command::SUCCESS;
    }
}
