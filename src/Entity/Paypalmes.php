<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Regex("/https:\/\/paypal.me\/[\w]*$/", message="paypal.link.invalid")
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
     * @Assert\Email(message = "paypal.email.invalid")
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
        return rtrim(trim($this->link), '/');
    }

    public function setLink(string $link): self
    {
        // make sure there is no trailing slash
        $link = rtrim(trim($link), '/');
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

    public function getBarOnly(): bool
    {
        return $this->bar !== null && $this->bar >= new DateTime();
    }
}
