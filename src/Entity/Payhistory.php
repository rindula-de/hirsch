<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payhistory
 *
 * @ORM\Table(name="payhistory", indexes={@ORM\Index(name="paypalme_id", columns={"paypalme_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\PayhistoryRepository")
 */
class Payhistory
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
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created = '';

    /**
     * @var \Paypalmes
     *
     * @ORM\ManyToOne(targetEntity="Paypalmes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="paypalme_id", referencedColumnName="id")
     * })
     */
    private $paypalme;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPaypalme(): ?Paypalmes
    {
        return $this->paypalme;
    }

    public function setPaypalme(?Paypalmes $paypalme): self
    {
        $this->paypalme = $paypalme;

        return $this;
    }


}
