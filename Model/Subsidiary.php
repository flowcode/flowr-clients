<?php

namespace Flower\ClientsBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Subsidiary
 *
 */
class Subsidiary
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"public_api"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"public_api"})
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255,nullable=true)
     * @Groups({"public_api"})
     */
    protected $street;

    /**
     * @var integer
     *
     * @ORM\Column(name="streetNumber", type="integer",nullable=true)
     * @Groups({"public_api"})
     */
    protected $streetNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="department", type="string", length=255,nullable=true)
     * @Groups({"public_api"})
     */
    protected $department;

    /**
     * @var string
     *
     * @ORM\Column(name="locality", type="string", length=255,nullable=true)
     * @Groups({"public_api"})
     */
    protected $locality;

    /**
     * @var string
     *
     * @ORM\Column(name="zipCode", type="string", length=64,nullable=true)
     * @Groups({"public_api"})
     */
    protected $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=128,nullable=true)
     * @Groups({"public_api"})
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\Account")
     * @ORM\JoinColumn(name="account", referencedColumnName="id")
     */
    protected $account;

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
     * @return Subsidiary
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set street
     *
     * @param string $street
     * @return Subsidiary
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set streetNumber
     *
     * @param integer $streetNumber
     * @return Subsidiary
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    /**
     * Get streetNumber
     *
     * @return integer 
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * Set department
     *
     * @param string $department
     * @return Subsidiary
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set locality
     *
     * @param string $locality
     * @return Subsidiary
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return string 
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     * @return Subsidiary
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string 
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Subsidiary
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }
    
    /**
     * Set account
     *
     * @param \Flower\ModelBundle\Entity\Clients\Account $account
     * @return Subsidiary
     */
    public function setAccount(\Flower\ModelBundle\Entity\Clients\Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \Flower\ModelBundle\Entity\Clients\Account 
     */
    public function getAccount()
    {
        return $this->account;
    }
}
