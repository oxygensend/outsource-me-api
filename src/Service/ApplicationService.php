<?php

namespace App\Service;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Application;
use App\Entity\JobOffer;
use App\Event\Notification\JobOfferApplicationEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ApplicationService
{
    public function __construct(readonly private Security                 $security,
                                readonly private IriConverterInterface    $iriConverter,
                                readonly private AttachmentService        $attachmentService,
                                readonly private EntityManagerInterface   $em,
                                readonly private EventDispatcherInterface $dispatcher
    )
    {
    }

    public function createApplicationForJobOffer(string $jobOfferIri, string|null $description, array|null $attachments): void
    {
        /** @var JobOffer $jobOffer */
        $jobOffer = $this->iriConverter->getResourceFromIri($jobOfferIri);
        $user = $this->security->getUser();

        if (!$jobOffer) {
            throw new BadRequestHttpException("JobOffer was not found.");
        }

        $applicationFromThisUser = $this->em->getRepository(Application::class)
            ->findApplicationsForJobOfferFromUser($jobOffer, $user);

        if (count($applicationFromThisUser) > 0) {
            throw new AccessDeniedHttpException('You have applicated for this offer yet');
        }

        $application = new Application();
        $application->setJobOffer($jobOffer);
        $application->setStatus(Application::APPLICATION_STATUS_OPEN);
        $application->setIndividual($user);
        $application->setDescription($description);
        $jobOffer->increaseNumberOfApplications();

        if ($attachments) {
            foreach ($attachments as $attachment) {
                $application->addAttachment($this->attachmentService->saveAttachment($attachment));
            }
        }

        $this->em->persist($application);
        $this->em->flush();

        /* Send notification to job offer author  */

        $this->dispatcher->dispatch(new JobOfferApplicationEvent($application));


    }

}