<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\AboutUsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation as Serializer;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[ORM\Entity(repositoryClass: AboutUsRepository::class)]
#[ApiResource(
    operations:[ new GetCollection(
        uriTemplate: '/about_us',
        cacheHeaders: [
            'max_age' => 86400,
            'shared_max_age' => 86400,
        ],
        paginationEnabled: false,
        normalizationContext: ["groups" => "about_us:get"],
    )]
)]
#[Vich\Uploadable]
class AboutUs extends AbstractEntity
{
    private const IMG_DIR = '/images/uploads/about_us';


    #[ORM\Column(length: 255)]
    #[Serializer\Groups("about_us:get")]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Serializer\Groups("about_us:get")]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[UploadableField(mapping: "image_about_us", fileNameProperty: "imageName")]
    private ?File $imageFile = null;

    #[ORM\Column]
    private bool $enabled = false;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    #[Serializer\SerializedName('mainImagePath')]
    #[Serializer\Groups("about_us:get")]
    public function getImagePath(): ?string
    {
        if ($this->imageName) {
            return self::IMG_DIR . '/' . $this->imageName;
        }
        return '';
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }


}
