<?php

namespace App\Parser;

use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PostalCodesParser implements ParserInterface
{
    private string $parseUrl;
    private string $destinationDir;
    private string $openMapUrl;
    private const TEMP_FILE = "tempFile.zip";
    private string $fileName;

    public function __construct(readonly private EntityManagerInterface $em,
                                readonly private ParameterBagInterface  $parameterBag,
                                readonly private HttpClientInterface    $client,
                                readonly private LoggerInterface        $logger
    )
    {
        $this->parseUrl = $this->parameterBag->get('postal_code_date_url');
        $this->openMapUrl = $this->parameterBag->get('open_street_map_url');
        $this->destinationDir = $this->parameterBag->get('kernel.project_dir') . '/var';
    }


    /**
     * @throws \Exception
     */
    public function parse(): void
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

    private function getCoordinates(Address $address): void
    {
        $response = $this->client->request('GET', $this->openMapUrl . '&postalcode=' . explode(',', $address->getPostCodes())[0]);
        $data = json_decode($response->getContent(), true);

        $address->setLat($data[0]['lat'] ?? null);
        $address->setLon($data[0]['lon'] ?? null);
    }

    private function extractPostCodes(array $results): array
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
    private function readDataFromFile(): array
    {
        $this->downloadCSVFromatFile();
        $firstRow = true;
        $results = [];
        $columns = [];

        $file = $this->destinationDir . '/' . $this->fileName;

        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if ($firstRow) {
                    $columns = $row;
                    $firstRow = false;
                } else {
                    $tmpRow = [];
                    for ($i = 0; $i < count($row); $i++) {
                        $tmpRow[$columns[$i]] = $row[$i];
                    }

                    $results[] = $tmpRow;

                }
            }
        }
        fclose($handle);

        $this->removeFile($file);
        $this->removeFile($this->destinationDir . '/' . self::TEMP_FILE);

        return $results;
    }

    /**
     * @throws \Exception
     */
    private function downloadCSVFromatFile(): void
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

    private function removeFile(string $file)
    {

        unlink($file);
    }
}