<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\PaypalmesRepository")]
class Paypalmes
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[Assert\Regex("/https:\/\/paypal.me\/[\w]*$/", message: 'paypal.link.invalid')]
    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $link;

    #[ORM\Column(type: 'string', length: 100, nullable: false, options: ['default' => ''])]
    private string $name = '';

    #[Assert\Email(message: 'paypal.email.invalid')]
    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['default' => 'NULL'])]
    private ?string $email = 'NULL';

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $bar;

    public function __construct(?Uuid $id = null)
    {
        $this->id = $id ?? Uuid::v7();
    }

    public function getId(): Uuid
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
        return null !== $this->bar && $this->bar >= new \DateTime();
    }
}
