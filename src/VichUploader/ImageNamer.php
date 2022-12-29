<?php

namespace App\VichUploader;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Vich\UploaderBundle\Exception\NameGenerationException;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\ConfigurableInterface;
use Vich\UploaderBundle\Naming\NamerInterface;

class ImageNamer implements NamerInterface, ConfigurableInterface
{
    const AVAILABLE_EXTENSIONS = ['jpeg', 'png', 'webp'];


    private ?string $extension;
    private string $propertyPath;


    public function configure(array $options): void
    {
        if (empty($options['extension'])) {
            throw new \InvalidArgumentException('Option "extension" is missing or empty.');
        }
        if (!in_array($options['extension'], self::AVAILABLE_EXTENSIONS)) {
            throw new \InvalidArgumentException(sprintf('Option "extension" is invalid. Available options: %s', implode(',', self::AVAILABLE_EXTENSIONS)));
        }

        $this->propertyPath = $options['property'] ?? '';
        $this->extension = $options['extension'];

    }

    public function name($object, PropertyMapping $mapping): string
    {

        try {
            $name = Urlizer::urlize($this->getPropertyValue($object, $this->propertyPath));

        } catch (NoSuchPropertyException $e) {
            $name = \str_replace('.', '', \uniqid('', true));
        }

        if ($name === null) {
            throw new NameGenerationException(\sprintf('File name could not be generated: property %s is empty.', $this->propertyPath));
        }


        return sprintf('%s.%s', $name, $this->extension);
    }

    private function getPropertyValue($object, $propertyPath)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($object, $propertyPath);
    }
}