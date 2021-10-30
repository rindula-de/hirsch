<?php

namespace AppEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payhistory
 *
 * @ORM\Table(name="payhistory", indexes={@ORM\Index(name="paypalme_id", columns={"paypalme_id"})})
 * @ORM\Entity
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
     * @ORM\Column(name="created", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $created = 'current_timestamp()';

    /**
     * @var \Paypalmes
     *
     * @ORM\ManyToOne(targetEntity="Paypalmes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="paypalme_id", referencedColumnName="id")
     * })
     */
    private $paypalme;


}
