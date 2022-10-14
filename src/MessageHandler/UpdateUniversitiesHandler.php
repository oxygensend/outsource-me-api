<?php

namespace App\MessageHandler;

use App\Entity\University;
use App\Message\UpdateUniversities;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final class UpdateUniversitiesHandler
{
    public function __construct(readonly private HttpClientInterface    $client,
                                readonly private CacheInterface         $cache,
                                readonly private LoggerInterface        $logger,
                                readonly private EntityManagerInterface $em)
    {
    }


    public function __invoke(UpdateUniversities $message): void
    {

        try {

            $data = $this->getCachedResponse($message->getUrl());
            $i = 0;

            foreach ($data as $university) {
                $uni = $this->em->getRepository(University::class)->findOneBy(['name' => $university['name']]);
                if (!$uni) {
                    $uni = new University();
                    $uni->setName($university['name']);
                    $this->em->persist($uni);

                    $i++;
                }

            }
            $this->em->flush();
            $this->logger->info('Successfully updated ' . $i . ' universities');

        } catch (\Exception $e) {

            $this->logger->warning('ERROR:UNIVERSITIES UPDATE -' . $e->getMessage() . $e->getFile() . $e->getLine());
        }


    }


    private function getCachedResponse(string $url): array
    {

        return $this->cache->get('polon_data', function (CacheItemInterface $cacheItem) use ($url) {
            $cacheItem->expiresAfter(5);
            $response = $this->client->request('GET', $url);
            return json_decode($response->getContent(), true)['institutions'];
        });
    }
}
