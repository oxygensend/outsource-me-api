<?php

namespace App\MessageHandler;

use App\Entity\JobOffer;
use App\Message\UpdateJobOffersOrder;
use App\Service\DisplayOrderService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsMessageHandler]
final class UpdateJobOffersOrderHandler
{
    public function __construct(readonly private EntityManagerInterface $em,
                                readonly private DisplayOrderService    $orderService,
                                readonly private LoggerInterface        $logger,
                                readonly private Stopwatch              $stopwatch)
    {
    }

    public function __invoke(UpdateJobOffersOrder $message): void
    {
        $this->stopwatch->start('updateJobOffersOrder');
        $this->logger->info('Started dispatching message: UpdateJobOffersOrder');

        $jobOffers = $this->em->getRepository(JobOffer::class)->findAllEnabled();
        $i = 0;

        foreach ($jobOffers as $jobOffer) {

            try {
                $this->orderService->calculateJobOfferPopularityRate($jobOffer);
            } catch (\Exception $exception) {
                $this->logger->error('Problem with setting order for job offer: ' . $jobOffer->getId() . ' ' . $exception->getMessage());
            }

            if (!$i % 200) {
                $this->em->flush();
                $i = 0;
            }

            $i++;
        }

        $this->em->flush();

        $time = $this->stopwatch->stop('updateJobOffersOrder');
        $this->logger->info('Ended dispatching message: UpdateJobOffersOrder time: ' . $time->getDuration() . ' ms');
        $this->logger->info('Successfully updated job offers order');
    }
}