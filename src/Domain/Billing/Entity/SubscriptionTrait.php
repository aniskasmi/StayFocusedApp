<?php

namespace App\Domain\Billing\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SubscriptionTrait
{
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected ?\DateTimeImmutable $subscriptionEnd = null;

    public function isSubscribe(): bool
    {
        return $this->subscriptionEnd > new \DateTime();
    }

    public function getSubscriptionEnd(): ?\DateTimeImmutable
    {
        return $this->subscriptionEnd;
    }

    public function setSubscriptionEnd(?\DateTimeImmutable $subscriptionEnd): self
    {
        $this->subscriptionEnd = $subscriptionEnd;

        return $this;
    }
}
