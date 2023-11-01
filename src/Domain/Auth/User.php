<?php

namespace App\Domain\Auth;

use App\Domain\Billing\Entity\SubscriptionTrait;
use App\Domain\Forum\Entity\ForumReaderUserInterface;
use App\Domain\Notification\Entity\Notifiable;
use App\Domain\Premium\Entity\PremiumTrait;
use App\Domain\Profile\Entity\DeletableTrait;
use App\Http\Twig\CacheExtension\CacheableInterface;
use App\Infrastructure\Payment\Stripe\StripeEntity;
use App\Infrastructure\Social\Entity\SocialLoggableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Auth\UserRepository")
 * @ORM\Table(name="`user`")
 * @Vich\Uploadable()
 * @UniqueEntity(fields={"email"}, repositoryMethod="findByCaseInsensitive")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use StripeEntity;
    use SubscriptionTrait;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=100)
     * @Assert\Email()
     */
    private string $email = '';
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $firstName = '';
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $lastName = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $password = '';

    /** @var array<string> */
    private array $roles = ['ROLE_USER'];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="string", length=2, nullable=true, options={"default": "FR"})
     */
    private ?string $country = null;

    /**
     * @ORM\Column(type="string", options={"default": null}, nullable=true)
     */
    private ?string $theme = null;

    /**
     * @ORM\Column(type="string", options={"default": null}, nullable=true)
     */
    private ?string $lastLoginIp = null;

    /**
     * @ORM\Column(type="datetime", options={"default": null}, nullable=true)
     */
    private ?\DateTimeInterface $lastLoginAt = null;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;
    
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
    

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email ?: '';

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password ?: '';

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function __serialize(): array
    {
        return [
            $this->id,
            $this->email,
            $this->password,
        ];
    }

    public function __unserialize(array $data): void
    {
        if (count($data) === 3) {
            [
                $this->id,
                $this->email,
                $this->password,
            ] = $data;
        }
    }

    public function getCountry(): string
    {
        return $this->country ?: 'FR';
    }

    public function setCountry(?string $country): User
    {
        $this->country = $country;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): User
    {
        $this->theme = $theme;

        return $this;
    }

    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    public function setLastLoginIp(?string $lastLoginIp): User
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): User
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }
    
    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    
    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }
    
    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }
    
    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
    
    public function getIdentity(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
