<?php

namespace Flower\ClientsBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * OpportunityStatus
 */
abstract class OpportunityStatus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="won", type="boolean")
     */
    protected $won;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isWon()
    {
        return $this->won;
    }

    /**
     * @param boolean $won
     */
    public function setWon($won)
    {
        $this->won = $won;
    }


}
