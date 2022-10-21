<?php

namespace App\Parser;

use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostalCodesParser implements ParserInterface
{
    private string $parseUrl;
    private string $destinationDir;
    private const TEMP_FILE = "tempFile.zip";
    private string $fileName;

    public function __construct(readonly private EntityManagerInterface $em,
                                readonly private ParameterBagInterface  $parameterBag,
    )
    {
        $this->parseUrl = $this->parameterBag->get('postal_code_date_url');
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
            $address->setCity($city);
            $address->setPostCodes(implode(',', $postCode));
            $this->em->persist($address);

            if (!$iter % 200) {
                $iter = 0;
                $this->em->flush();
            }
            $iter++;
        }

        $this->em->flush();

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