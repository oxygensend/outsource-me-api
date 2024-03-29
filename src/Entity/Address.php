<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Filter\AddressSearchFilter;
use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ApiResource(
    operations: [
        new GetCollection(
            cacheHeaders: [
                'max_age' => 84600,
                'shared_max_age' => 84600,
            ],
            paginationEnabled: false,
            normalizationContext: ['groups' => 'address:list']
        ),
    ],
)]
#[ApiFilter(AddressSearchFilter::class)]
#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address extends AbstractEntity
{
    #[Serializer\Groups(['user:profile','jobOffer:one'])]
    #[ORM\Column( type: "text")]
    private ?string $postCodes = null;

    #[Serializer\Groups(['user:profile', 'address:list', 'jobOffer:get', 'jobOffer:one', 'user:get'])]
    #[ORM\Column(length: 255)]
    private ?string $city = null;


    #[ORM\OneToMany(mappedBy: 'address', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'address', targetEntity: JobOffer::class)]
    private Collection $jobOffers;

    #[ORM\Column(nullable: true)]
    private ?float $lon = null;

    #[ORM\Column(nullable: true)]
    private ?float $lat = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->jobOffers = new ArrayCollection();
        parent::__construct();
    }

    public function __toString(): string
    {
        return $this->city;
    }

    public function getPostCodes(): ?string
    {
        return $this->postCodes;
    }

    public function setPostCodes(string $postCodes): self
    {
        $this->postCodes = $postCodes;

        return $this;

    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }


    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setAddress($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getAddress() === $this) {
                $user->setAddress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, JobOffer>
     */
    public function getJobOffers(): Collection
    {
        return $this->jobOffers;
    }

    public function addJobOffer(JobOffer $jobOffer): self
    {
        if (!$this->jobOffers->contains($jobOffer)) {
            $this->jobOffers->add($jobOffer);
            $jobOffer->setAddress($this);
        }

        return $this;
    }

    public function removeJobOffer(JobOffer $jobOffer): self
    {
        if ($this->jobOffers->removeElement($jobOffer)) {
            // set the owning side to null (unless already changed)
            if ($jobOffer->getAddress() === $this) {
                $jobOffer->setAddress(null);
            }
        }

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(?float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }
}
