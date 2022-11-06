<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\DownloadAttachmentAction;
use App\Controller\Api\PostApplicationController;
use App\Repository\AttachmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Symfony\Component\Serializer\Annotation as Serializer;

#[Uploadable]
#[ApiResource(
    operations:[
        new Get(
            controller: DownloadAttachmentAction::class,
            openapiContext: [
                'summary' => 'Download attachment file',
                'description' => 'Download attachment file',
                'responses' => [
                    '200' => null,
                    '201' => [
                        'description' => 'binary file'
                    ],
                    '400' => null,
                    '401' => null,
                    '422' => null
                ]
            ],
            security: "is_granted('DOWNLOAD_ATTACHMENT', object)"
        )
    ]
)]
#[ORM\Entity(repositoryClass: AttachmentRepository::class)]
class Attachment extends AbstractEntity
{

    #[Serializer\Groups(['application:one'])]
    #[ORM\Column(length: 255)]
    private ?string $originalName = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[UploadableField(mapping: 'attachment_file', fileNameProperty: 'name', size: 'size')]
    private ?File $file = null;

    #[ORM\ManyToOne(inversedBy: 'attachments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'attachments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Application $application = null;


    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        if ($file) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

}
