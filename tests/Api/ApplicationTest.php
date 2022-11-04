<?php

namespace App\Tests\Api;

use App\Entity\Application;
use App\Entity\Notification;

class ApplicationTest extends AbstractApiTestCase
{

    public function testAddApplication(): void
    {
        $projectDir = static::getContainer()->get('kernel')->getProjectDir();

//        $file1 = new UploadedFile($projectDir . '/fixtures/files/simple_pdf.pdf', 'file1.pdf');
//        $file2 = new UploadedFile($projectDir  . '/fixtures/files/simple_pdf_1.pdf' ,'file2.pdf');
        $token = $this->loginRequest(self::DEVELOPER_CREDENTIALS)->toArray()['token'];
        $response = $this->createMultiPartRequest(
            uri: '/api/applications',
            extra: [
                'parameters' => [

                    'jobOffer' => '/api/job_offers/job-offer-test',
                    'description' => 'Test'

                ],
                'files' => [
//                    'attachments' => [$file1, $file2]
                ]

            ],
            token: $token
        );

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailHasHeader($email, 'subject', 'Outsource me - masz nową aplikacje na oferte X');

        $application = $this->em->getRepository(Application::class)->findBy(['description' => 'Test']);
        $notifications = $this->em->getRepository(Notification::class)->findBy(['relatedApplication' => $application]);

        $this->assertEquals(2, count($notifications));

//        $files = $em->getRepository(Attachment::class)->findBy(['A']);
        $this->assertResponseIsSuccessful();

    }

}