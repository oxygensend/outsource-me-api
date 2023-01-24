<?php

namespace App\Parser;

use App\Entity\Technology;

class TechnologyParser extends AbstractParser implements ParserInterface
{
    private const FILE_NAME = 'technologies.csv';


    public function parse()
    {
        $file = $this->parameterBag->get('kernel.project_dir') . '/storage' . '/' . self::FILE_NAME;
        $data = $this->readDataFromCSVFile($file);
        $technologies = $this->em->getRepository(Technology::class)->findAll();
        foreach ($technologies as $technology) {
            $this->em->remove($technology);
        }

        foreach ($data as $row) {
            try {
                $technology = new Technology();
                $technology->setName($row['technology']);
                $this->em->persist($technology);
            } catch (\Exception $exception) {
                $this->logger->error('Problem with setting address values ', ['e' => $exception]);
            }
        }
        $this->em->flush();

    }

}