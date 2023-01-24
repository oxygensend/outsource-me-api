<?php

namespace App\Parser;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AbstractParser
{
    public function __construct( readonly protected ParameterBagInterface  $parameterBag,
                                readonly protected EntityManagerInterface $em,
                                readonly protected LoggerInterface        $logger)
    {
    }

    protected function readDataFromCSVFile(string $file, string $seperator = ','): array
    {
        $firstRow = true;
        $results = [];
        $columns = [];

        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, $seperator)) !== FALSE) {
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

        return $results;
    }
}