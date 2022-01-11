<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paypalmes.
 *
 * @ORM\Table(name="paypalmes")
 * @ORM\Entity(repositoryClass="App\Repository\PaypalmesRepository")
 */
class Paypalmes
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=100, nullable=false)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false, options={"default"=""})
     */
    private $name = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $email = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="bar", type="date", nullable=true)
     */
    private $bar;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBar(): ?\DateTime
    {
        return $this->bar;
    }

    public function setBar(?\DateTime $bar): self
    {
        $this->bar = $bar;

        return $this;
    }
}
