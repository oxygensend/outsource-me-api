<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
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
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;


#[
    ApiResource(
        operations: [
            new GetCollection(
                paginationItemsPerPage: 10
            ),
            new Post(
                security: "is_granted('CREATE_JOB_OFFER')",
                processor: JobOfferProcessor::class
            ),
            new Patch(
                uriVariables: [
                    'id' => new Link(parameterName: 'id', fromClass: JobOffer::class, identifiers: ['id'])
                ],
                security: "is_granted('EDIT_JOB_OFFER', object)",
            ),
            new Delete(
                uriVariables: [
                    'id' => new Link(parameterName: 'id', fromClass: JobOffer::class, identifiers: ['id'])
                ],
                security: "is_granted('DELETE_JOB_OFFER', object)",
                processor: DeleteJobOfferProcessor::class
            ),
            new Get(
                uriTemplate: '/job_offers/{slug}',
                uriVariables: [
                    'slug' => new Link(parameterName: 'slug', fromClass: JobOffer::class, identifiers: ['slug'])
                ],
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

    const EXPERIENCE_CHOICES = ['Senior', 'Junior', 'Mid', 'Expert', 'Stażysta'];


    #[Assert\NotBlank]
    #[Serializer\Groups(['jobOffer:get', 'jobOffer:write', 'jobOffer:one', 'user:profile-principle','application:one', 'application:users'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Serializer\Groups(['jobOffer:get', 'jobOffer:write', 'jobOffer:one'])]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Serializer\Groups(['jobOffer:write', 'jobOffer:one'])]
    #[ORM\ManyToMany(targetEntity: WorkType::class)]
    private Collection $workType;

    #[Assert\Valid]
    #[Serializer\Groups(['jobOffer:write', 'jobOffer:one'])]
    #[ORM\OneToOne(cascade: ['remove', 'persist'])]
    private ?SalaryRange $salaryRange = null;

    #[ORM\Column]
    private ?int $redirectCount = 0;

    #[ORM\OneToMany(mappedBy: 'jobOffer', targetEntity: Application::class, cascade: ["remove"])]
    private Collection $applications;

    #[Assert\NotBlank]
    #[Serializer\Groups(['jobOffer:write', 'jobOffer:one'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormOfEmployment $formOfEmployment = null;

    #[Serializer\Groups(['jobOffer:write', 'jobOffer:one', 'user:profile-principle'])]
    #[ORM\ManyToOne(inversedBy: 'jobOffers')]
    private ?Address $address = null;

    #[Serializer\Groups(['jobOffer:get', 'jobOffer:one', 'application:one'])]
    #[ORM\ManyToOne(inversedBy: 'jobOffers')]
    private ?User $user = null;

    #[Serializer\Groups(['jobOffer:get', 'jobOffer:one', 'user:profile-principle'])]
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

    #[Slug(fields: ['name'])]
    #[ApiProperty(identifier: true)]
    #[Serializer\Groups(['jobOffer:one', 'jobOffer:get', 'user:profile-principle'])]
    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private string $slug;

    #[Assert\Choice(choices: self::EXPERIENCE_CHOICES, message: "The {{ value }} is not a valid choice.Valid choices: {{ choices }}")]
    #[Serializer\Groups(['jobOffer:write', 'jobOffer:one'])]
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $experience = null;

    #[Serializer\Groups(['jobOffer:write', 'jobOffer:one'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $validTo = null;


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

    #[Serializer\SerializedName("shortDescription")]
    #[Serializer\Groups(['jobOffer:get', 'user:profile-principle'])]
    public function getShortDescription(): string
    {
        return substr($this->description, 0, 100);
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

    public function getSalaryRange(): ?SalaryRange
    {
        return $this->salaryRange;
    }

    public function setSalaryRange(?SalaryRange $salaryRange): self
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(?string $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->validTo;
    }

    public function setValidTo(?\DateTimeInterface $validTo): self
    {
        $this->validTo = $validTo;

        return $this;
    }


}
