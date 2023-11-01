<?php

namespace App\Domain\Billing\Entity;

use App\Infrastructure\Payment\Stripe\StripeEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Billing\Repository\PlanRepository")
 */
class Plan
{
    use StripeEntity;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $name = '';
    
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $shortDescription = '';
    
    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private array $longDescription = [''];
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $popular = false;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private float $price = 0;

    /**
     * Durée de l'abonnement (en mois).
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $duration = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Plan
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Plan
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): Plan
    {
        $this->price = $price;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): Plan
    {
        $this->duration = $duration;

        return $this;
    }

    public function getStripeId(): ?string
    {
        return $this->stripeId;
    }

    public function setStripeId(?string $stripeId): Plan
    {
        $this->stripeId = $stripeId;

        return $this;
    }
    
    /**
     * @return bool
     */
    public function isPopular(): bool
    {
        return $this->popular;
    }
    
    /**
     * @param bool $popular
     */
    public function setPopular(bool $popular): void
    {
        $this->popular = $popular;
    }
    
    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }
    
    /**
     * @param string $shortDescription
     */
    public function setShortDescription(string $shortDescription): void
    {
        $this->shortDescription = $shortDescription;
    }
    
    /**
     * @return array|string[]
     */
    public function getLongDescription(): array
    {
        return $this->longDescription;
    }
    
    /**
     * @param array|string[] $longDescription
     */
    public function setLongDescription(array $longDescription): void
    {
        $this->longDescription = $longDescription;
    }
}
