<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\OrdersRepository")]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 1000, nullable: false, options: ['default' => ''])]
    private string $note = '';

    #[ORM\Column(type: 'date', nullable: false)]
    private \DateTime $for_date;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $created;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Bitte gib einen Namen ein.')]
    private string $orderedby = '';

    #[ORM\ManyToOne(targetEntity: Hirsch::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Hirsch $hirsch;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getForDate(): ?\DateTime
    {
        return $this->for_date;
    }

    public function setForDate(\DateTime $forDate): self
    {
        $this->for_date = $forDate;

        return $this;
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

    public function getOrderedby(): ?string
    {
        return $this->orderedby;
    }

    public function setOrderedby(string $orderedby): self
    {
        $this->orderedby = $orderedby;

        return $this;
    }

    public function getHirsch(): ?Hirsch
    {
        return $this->hirsch;
    }

    public function setHirsch(Hirsch $hirsch): self
    {
        $this->hirsch = $hirsch;

        return $this;
    }
}
