<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: "App\Repository\PayhistoryRepository")]
class Payhistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $created;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Paypalmes::class)]
    private Paypalmes $paypalme;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $clickedBy;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getPaypalme(): ?Paypalmes
    {
        return $this->paypalme;
    }

    public function setPaypalme(?Paypalmes $paypalme): self
    {
        $this->paypalme = $paypalme;

        return $this;
    }

    /**
     * Get the value of clickedBy.
     */
    public function getClickedBy(): ?string
    {
        return $this->clickedBy;
    }

    /**
     * Set the value of clickedBy.
     */
    public function setClickedBy(string $clickedBy): self
    {
        $this->clickedBy = $clickedBy;

        return $this;
    }
}
