<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Filter\JobOfferOrderFilter;
use App\Filter\WorkTypesFilter;
use App\Filter\TechnologiesFilter;
use App\Repository\JobOfferRepository;
use App\State\DeleteJobOfferProcessor;
use App\State\JobOfferProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;


#[ApiResource(
    operations: [
        new GetCollection(
            paginationItemsPerPage: 14
        ),
        new Post(
            security: "is_granted('CREATE_JOB_OFFER')",
            processor: JobOfferProcessor::class
        ),
        new Patch(
            security: "is_granted('EDIT_JOB_OFFER', object)"
        ),
        new Delete(
            security: "is_granted('DELETE_JOB_OFFER', object)",
            processor: DeleteJobOfferProcessor::class
        ),
        new Get(
            normalizationContext: ['groups' => 'jobOffer:one']
        )
    ],

    normalizationContext: ['groups' => 'jobOffer:get'],
    denormalizationContext: ['groups' => 'jobOffer:write'],
)


]
#[ApiFilter(WorkTypesFilter::class)]
#[ApiFilter(TechnologiesFilter::class)]
#[ApiFilter(JobOfferOrderFilter::class)]
#[ApiFilter(SearchFilter::class, properties: [
    'address.id' => 'exact',
    'formOfEmployment.id' => 'exact'])]
#[ORM\Entity(repositoryClass: JobOfferRepository::class)]
class JobOffer extends AbstractEntity
{

    #[Assert\NotBlank]
    #[Serializer\Groups(['jobOffer:get', 'jobOffer:write', 'jobOffer:one'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Serializer\Groups(['jobOffer:get', 'jobOffer:write', 'jobOffer:one'])]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Serializer\Groups(['jobOffer:write', 'jobOffer:one'])]
    #[ORM\ManyToMany(targetEntity: WorkType::class)]
    private Collection $workType;

    #[Serializer\Groups(['jobOffer:get', 'jobOffer:write', 'jobOffer:one'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $salaryRange = null;

    #[ORM\Column]
    private ?int $redirectCount = 0;

    #[ORM\OneToMany(mappedBy: 'jobOffer', targetEntity: Application::class, cascade: ["remove"])]
    private Collection $applications;

    #[Assert\NotBlank]
    #[Serializer\Groups(['jobOffer:write', 'jobOffer:one'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormOfEmployment $formOfEmployment = null;

    #[Serializer\Groups(['jobOffer:write', 'jobOffer:get', 'jobOffer:one'])]
    #[ORM\ManyToOne(inversedBy: 'jobOffers')]
    private ?Address $address = null;

    #[Serializer\Groups(['jobOffer:get', 'jobOffer:one'])]
    #[ORM\ManyToOne(inversedBy: 'JobOffers')]
    private ?User $user = null;

    #[Serializer\Groups(['jobOffer:get', 'jobOffer:one'])]
    #[ORM\Column(nullable: true)]
    private ?int $numberOfApplications = 0;

    #[ORM\Column]
    private ?bool $archived = false;

    #[Serializer\Groups(['jobOffer:write', 'jobOffer:one'])]
    #[ORM\ManyToMany(targetEntity: Technology::class)]
    private Collection $technologies;

    #[ORM\Column(nullable: true)]
    private ?int $displayOrder = null;

    #[ORM\Column(nullable: true)]
    private ?int $popularityOrder = null;


    public function __construct()
    {
        parent::__construct();
        $this->workType = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->technologies = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, WorkType>
     */
    public function getWorkType(): Collection
    {
        return $this->workType;
    }

    public function addWorkType(WorkType $workType): self
    {
        if (!$this->workType->contains($workType)) {
            $this->workType->add($workType);
        }

        return $this;
    }

    public function removeWorkType(WorkType $workType): self
    {
        $this->workType->removeElement($workType);

        return $this;
    }

    public function getSalaryRange(): ?string
    {
        return $this->salaryRange;
    }

    public function setSalaryRange(?string $salaryRange): self
    {
        $this->salaryRange = $salaryRange;

        return $this;
    }

    public function getRedirectCount(): ?int
    {
        return $this->redirectCount;
    }

    public function setRedirectCount(int $redirectCount): self
    {
        $this->redirectCount = $redirectCount;

        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setJobOffer($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getJobOffer() === $this) {
                $application->setJobOffer(null);
            }
        }

        return $this;
    }

    public function getFormOfEmployment(): ?FormOfEmployment
    {
        return $this->formOfEmployment;
    }

    public function setFormOfEmployment(?FormOfEmployment $formOfEmployment): self
    {
        $this->formOfEmployment = $formOfEmployment;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNumberOfApplications(): ?int
    {
        return $this->numberOfApplications;
    }

    public function increaseNumberOfApplications(): self
    {
        $this->numberOfApplications++;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * @return Collection<int, Technology>
     */
    public function getTechnologies(): Collection
    {
        return $this->technologies;
    }

    public function addTechnology(Technology $technology): self
    {
        if (!$this->technologies->contains($technology)) {
            $this->technologies->add($technology);
        }

        return $this;
    }

    public function removeTechnology(Technology $technology): self
    {
       $this->technologies->removeElement($technology);

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(?int $displayOrder): self
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    public function getPopularityOrder(): ?int
    {
        return $this->popularityOrder;
    }

    public function setPopularityOrder(?int $popularityOrder): self
    {
        $this->popularityOrder = $popularityOrder;

        return $this;
    }


}
