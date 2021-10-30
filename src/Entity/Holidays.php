<?php

namespace AppEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Holidays
 *
 * @ORM\Table(name="holidays")
 * @ORM\Entity
 */
class Holidays
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
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="date", nullable=false)
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="date", nullable=false)
     */
    private $end;


}
