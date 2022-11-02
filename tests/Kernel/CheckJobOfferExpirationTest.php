<?php

namespace App\Tests\Kernel;

use App\Entity\JobOffer;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CheckJobOfferExpirationTest extends KernelTestCase
{
    public function testArchivingExpiredJobOffers(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:check-job-offer-expiration');
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $jobOfferRepository = $em->getRepository(JobOffer::class);

        /** @var JobOffer $jobOffer */
        $jobOffer = $jobOfferRepository->find(1);
        $jobOffer->setValidTo((new \DateTime())->setTime(0, 0, 0));
        $jobOffer->setArchived(false);
        $em->flush();

        $this->assertEquals(false, $jobOffer->isArchived());

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $jobOfferAfter = $jobOfferRepository->find(1);

        $this->assertSame('test', $kernel->getEnvironment());
        $this->assertEquals(true, $jobOfferAfter->isArchived());
    }
}
