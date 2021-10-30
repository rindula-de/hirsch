<?php

namespace AppEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paypalmes
 *
 * @ORM\Table(name="paypalmes")
 * @ORM\Entity
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
     * @ORM\Column(name="name", type="string", length=100, nullable=false, options={"default"="''"})
     */
    private $name = '\'\'';

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $email = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="bar", type="date", nullable=true, options={"default"="NULL"})
     */
    private $bar = 'NULL';


}
