<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\ResetPasswordSendLinkAction;
use App\DTO\PasswordResetSendLinkDto;
use App\Repository\UserRepository;
use App\State\ResetPasswordSendLinkProcessor;
use App\State\UserRegistrationProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\IsPasswordConfirmed;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: "/register",
            security: "!is_granted('ROLE_USER')",
            processor: UserRegistrationProcessor::class
        ),
        new Post(
            uriTemplate: '/reset_password_send_link',
            controller: ResetPasswordSendLinkAction::class,
            openapiContext: [
                'summary' => 'Request password reset confirmation token',
                'description' => 'Request password reset confirmation token',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'email' => [
                                        'type' => 'string',
                                        'example' => 'test@example.com'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Email with configured link will be send to given user.'
                        ],
                    '201' => null,
                    '400' => null,
                    '401' => null,
                    '422' => null
                ]
            ],
            security: "!is_granted('ROLE_USER')"
        )

    ],
    normalizationContext: ["groups" => "user:read"],
    denormalizationContext: ["groups" => "user:register"]
)]
#[IsPasswordConfirmed]
#[UniqueEntity(fields: ['email'], message: 'Account with this email exists')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{

    private const ACCOUNT_TYPES = ['Developer', 'Principal', 'Admin'];
    private const ROLES = ['ROLE_DEVELOPER', 'ROLE_ADMIN', 'ROLE_EDITOR', 'ROLE_PRINCIPAL'];

    #[Serializer\Groups(['user:register', 'user:read'])]
    #[Assert\Email]
    #[Assert\NotBlank]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[Serializer\Groups(['user:register'])]
    #[Assert\Length(min: 2, max: 50,
        minMessage: "Name have to be at least 2 characters",
        maxMessage: "Name have to be no longer than 50 characters")]
    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Serializer\Groups(['user:register'])]
    #[Assert\Length(min: 2, max: 50,
        minMessage: "Surname have to be at least 2 characters",
        maxMessage: "Surname have to be no longer than 50 characters")]
    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $surname = null;

    #[ORM\Column(length: 9, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $githubUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedinUrl = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column]
    private int $redirectCount = 0;

    #[Serializer\Groups(['user:register'])]
    #[Assert\Choice([
        'Developer',
        'Principle'
    ])]
    #[ORM\Column(nullable: true)]
    private ?string $accountType = null;

    #[ORM\OneToMany(mappedBy: 'individual', targetEntity: JobPosition::class)]
    private Collection $jobPositions;

    #[ORM\OneToMany(mappedBy: 'individual', targetEntity: Education::class)]
    private Collection $educations;

    #[ORM\OneToMany(mappedBy: 'individual', targetEntity: Language::class)]
    private Collection $languages;

    #[ORM\OneToMany(mappedBy: 'toWho', targetEntity: Opinion::class, orphanRemoval: true)]
    private Collection $opinions;

    #[ORM\OneToMany(mappedBy: 'individual', targetEntity: Application::class)]
    private Collection $applications;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[Assert\Regex(
        pattern: '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
        message: 'Password have to be minimum 8 characters and contains at least one letter and number.'

    )]
    #[Assert\NotBlank]
    #[Serializer\Groups(['user:register'])]
    #[Serializer\SerializedName("password")]
    private ?string $plainPassword = null;

    #[Assert\NotBlank]
    #[Serializer\Groups(['user:register'])]
    private string $passwordConfirmation;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $emailConfirmedAt = null;


    public function __construct()
    {
        parent::__construct();
        $this->jobPositions = new ArrayCollection();
        $this->educations = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->opinions = new ArrayCollection();
        $this->applications = new ArrayCollection();
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getGithubUrl(): ?string
    {
        return $this->githubUrl;
    }

    public function setGithubUrl(?string $githubUrl): self
    {
        $this->githubUrl = $githubUrl;

        return $this;
    }

    public function getLinkedinUrl(): ?string
    {
        return $this->linkedinUrl;
    }

    public function setLinkedinUrl(?string $linkedinUrl): self
    {
        $this->linkedinUrl = $linkedinUrl;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

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

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(string $accountType): self
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * @return Collection<int, JobPosition>
     */
    public function getJobPositions(): Collection
    {
        return $this->jobPositions;
    }

    public function addJobPosition(JobPosition $jobPosition): self
    {
        if (!$this->jobPositions->contains($jobPosition)) {
            $this->jobPositions->add($jobPosition);
            $jobPosition->setIndividual($this);
        }

        return $this;
    }

    public function removeJobPosition(JobPosition $jobPosition): self
    {
        if ($this->jobPositions->removeElement($jobPosition)) {
            // set the owning side to null (unless already changed)
            if ($jobPosition->getIndividual() === $this) {
                $jobPosition->setIndividual(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Education>
     */
    public function getEducations(): Collection
    {
        return $this->educations;
    }

    public function addEducation(Education $education): self
    {
        if (!$this->educations->contains($education)) {
            $this->educations->add($education);
            $education->setIndividual($this);
        }

        return $this;
    }

    public function removeEducation(Education $education): self
    {
        if ($this->educations->removeElement($education)) {
            // set the owning side to null (unless already changed)
            if ($education->getIndividual() === $this) {
                $education->setIndividual(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Language>
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(Language $language): self
    {
        if (!$this->languages->contains($language)) {
            $this->languages->add($language);
            $language->setIndividual($this);
        }

        return $this;
    }

    public function removeLanguage(Language $language): self
    {
        if ($this->languages->removeElement($language)) {
            // set the owning side to null (unless already changed)
            if ($language->getIndividual() === $this) {
                $language->setIndividual(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Opinion>
     */
    public function getOpinions(): Collection
    {
        return $this->opinions;
    }

    public function addOpinion(Opinion $opinion): self
    {
        if (!$this->opinions->contains($opinion)) {
            $this->opinions->add($opinion);
            $opinion->setToWho($this);
        }

        return $this;
    }

    public function removeOpinion(Opinion $opinion): self
    {
        if ($this->opinions->removeElement($opinion)) {
            // set the owning side to null (unless already changed)
            if ($opinion->getToWho() === $this) {
                $opinion->setToWho(null);
            }
        }

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
            $application->setIndividual($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getIndividual() === $this) {
                $application->setIndividual(null);
            }
        }

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getPasswordConfirmation(): string
    {
        return $this->passwordConfirmation;
    }

    public function setPasswordConfirmation(string $passwordConfirmation): self
    {
        $this->passwordConfirmation = $passwordConfirmation;
        return $this;
    }

    public function getEmailConfirmedAt(): ?\DateTimeInterface
    {
        return $this->emailConfirmedAt;
    }

    public function setEmailConfirmedAt(?\DateTimeInterface $emailConfirmedAt): self
    {
        $this->emailConfirmedAt = $emailConfirmedAt;
        return $this;
    }

    #[Serializer\Groups("user:read")]
    #[Serializer\SerializedName("message")]
    public function getSuccessfullRegisterMessage(): string
    {
        return "Rejestracja powiodła się! Sprawdź podany adres email.";
    }

}
