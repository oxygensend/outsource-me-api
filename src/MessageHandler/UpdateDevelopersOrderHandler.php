<?php

namespace App\MessageHandler;

use App\Entity\JobOffer;
use App\Entity\User;
use App\Message\UpdateDevelopersOrder;
use App\Message\UpdateJobOffersOrder;
use App\Service\DisplayOrderService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsMessageHandler]
final class UpdateDevelopersOrderHandler
{
    public function __construct(readonly private EntityManagerInterface $em,
                                readonly private DisplayOrderService    $orderService,
                                readonly private LoggerInterface        $logger,
                                readonly private Stopwatch              $stopwatch)
    {
    }

    public function __invoke(UpdateDevelopersOrder $message): void
    {
        $this->stopwatch->start('updateDevelopersOrder');
        $this->logger->info('Started dispatching message: UpdateDevelopersOrder');

        $users = $this->em->getRepository(User::class)->findAllDevelopersLookingForJob();
        $i = 0;

        foreach ($users as $user) {

            try {
                $this->orderService->calculateDevelopersPopularityRate($user);
            } catch (\Exception $exception) {
                $this->logger->error('Problem with setting order for user: ' . $user->getId() . ' ' . $exception->getMessage());
            }

            if (!$i % 200) {
                $this->em->flush();
                $i = 0;
            }

            $i++;
        }

        $this->em->flush();

        $time = $this->stopwatch->stop('updateDevelopersOrder');
        $this->logger->info('Ended dispatching message: UpdateDevelopersOrder time: ' . $time->getDuration() . ' ms');
        $this->logger->info('Successfully updated Developers order');
    }
}