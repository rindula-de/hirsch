<?php

namespace AppEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Phinxlog
 *
 * @ORM\Table(name="phinxlog")
 * @ORM\Entity
 */
class Phinxlog
{
    /**
     * @var int
     *
     * @ORM\Column(name="version", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $version;

    /**
     * @var string|null
     *
     * @ORM\Column(name="migration_name", type="string", length=100, nullable=true, options={"default"="NULL"})
     */
    private $migrationName = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=true, options={"default"="NULL"})
     */
    private $startTime = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true, options={"default"="NULL"})
     */
    private $endTime = 'NULL';

    /**
     * @var bool
     *
     * @ORM\Column(name="breakpoint", type="boolean", nullable=false)
     */
    private $breakpoint = '0';


}
