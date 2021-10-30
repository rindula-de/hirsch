<?php

namespace AppEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orders
 *
 * @ORM\Table(name="orders", indexes={@ORM\Index(name="FK_orders_hirsch", columns={"name"})})
 * @ORM\Entity
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
     * @ORM\Column(name="note", type="string", length=1000, nullable=false, options={"default"="''"})
     */
    private $note = '\'\'';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="for", type="date", nullable=false)
     */
    private $for;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $created = 'current_timestamp()';

    /**
     * @var string
     *
     * @ORM\Column(name="orderedby", type="string", length=255, nullable=false)
     */
    private $orderedby;

    /**
     * @var \Hirsch
     *
     * @ORM\ManyToOne(targetEntity="Hirsch")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="name", referencedColumnName="slug")
     * })
     */
    private $name;


}
