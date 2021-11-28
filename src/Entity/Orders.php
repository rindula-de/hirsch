<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orders
 *
 * @ORM\Table(name="orders", indexes={@ORM\Index(name="FK_orders_hirsch", columns={"hirsch_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\OrdersRepository")
 */
class Orders
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=1000, nullable=false, options={"default"=""})
     */
    private $note = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="for_date", type="date", nullable=false)
     */
    private $for_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created = '';

    /**
     * @var string
     *
     * @ORM\Column(name="orderedby", type="string", length=255, nullable=false)
     */
    private $orderedby;

    /**
     * @ORM\ManyToOne(targetEntity=Hirsch::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $hirsch;

    public function getId(): ?int
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

    public function getForDate(): ?\DateTimeInterface
    {
        return $this->for_date;
    }

    public function setForDate(\DateTimeInterface $forDate): self
    {
        $this->for_date = $forDate;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
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

    public function setHirsch(?Hirsch $hirsch): self
    {
        $this->hirsch = $hirsch;

        return $this;
    }

}
