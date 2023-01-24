<?php

namespace App\Parser;

use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Project;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PostalCodesParser extends AbstractParser implements ParserInterface
{
    private string $parseUrl;
    private string $destinationDir;
    private string $openMapUrl;
    private const TEMP_FILE = "tempFile.zip";
    private string $fileName;

    public function __construct(EntityManagerInterface                 $em,
                                ParameterBagInterface                  $parameterBag,
                                LoggerInterface                        $logger,
                                readonly protected HttpClientInterface $client,
    )
    {
        parent::__construct($parameterBag, $em, $logger);
        $this->parseUrl = $parameterBag->get('postal_code_date_url');
        $this->openMapUrl = $parameterBag->get('open_street_map_url');
        $this->destinationDir = $parameterBag->get('kernel.project_dir') . '/var';
    }


    /**
     * @throws \Exception
     */
    public
    function parse(): void
    {
        $results = $this->readDataFromFile();

        $addressRepository = $this->em->getRepository(Address::class);
        $postCodes = $this->extractPostCodes($results);

        $iter = 0;
        foreach ($postCodes as $city => $postCode) {

            $address = $addressRepository->findOneBy(['city' => $city]);
            if (!$address) {
                $address = new Address();
            }
            try {

                $address->setCity($city);
                $address->setPostCodes(implode(',', $postCode));
                $this->getCoordinates($address);
                $this->em->persist($address);

                if (!$iter % 200) {
                    $iter = 0;
                    $this->em->flush();
                }

            } catch (\Exception $exception) {
                $this->logger->error('Problem with setting address values ', ['e' => $exception]);
            }

            $iter++;
        }

        $this->em->flush();

    }

    private
    function getCoordinates(Address $address): void
    {
        $response = $this->client->request('GET', $this->openMapUrl . '&postalcode=' . explode(',', $address->getPostCodes())[0]);
        $data = json_decode($response->getContent(), true);

        $address->setLat($data[0]['lat'] ?? null);
        $address->setLon($data[0]['lon'] ?? null);
    }

    private
    function extractPostCodes(array $results): array
    {
        $postCodes = [];
        foreach ($results as $result) {
            $voivodeship = explode(' ', $result['WOJEWÓDZTWO']);
            $city = $result['MIEJSCOWOŚĆ'] . ', Woj.' . $voivodeship[1];
            $postCodes[$city][] = $result['KOD POCZTOWY'];
        }
        return $postCodes;
    }

    /**
     * @throws \Exception
     */
    private
    function readDataFromFile(): array
    {
        $this->downloadCSVFromatFile();

        $file = $this->destinationDir . '/' . $this->fileName;
        $results = $this->readDataFromCSVFile($file, ';');

        $this->removeFile($file);
        $this->removeFile($this->destinationDir . '/' . self::TEMP_FILE);

        return $results;
    }

    /**
     * @throws \Exception
     */
    private
    function downloadCSVFromatFile(): void
    {
        $zipArchive = new \ZipArchive();
        $destinationDir = $this->parameterBag->get('kernel.project_dir') . '/var';
        $tmpFile = $destinationDir . '/' . self::TEMP_FILE;


        if (!copy($this->parseUrl, $tmpFile)) {
            throw new \Exception('Unable to copy zip content to temporary file');
        }

        if (!$zipArchive->open($tmpFile)) {
            $this->removeFile($tmpFile);
            throw new \Exception('Unable to open zip archive');
        }

        for ($i = 0; $i < $zipArchive->numFiles; $i++) {
            if (!$zipArchive->extractTo($destinationDir, array($zipArchive->getNameIndex($i)))) {
                $this->removeFile($tmpFile);
                throw  new \Exception('Unable to extract files from archive');
            }
            $this->fileName = $zipArchive->getNameIndex($i);
        }
    }

    private
    function removeFile(string $file)
    {

        unlink($file);
    }
}