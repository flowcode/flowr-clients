<?php

namespace Flower\ClientsBundle\Model;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;
use Flower\ModelBundle\Entity\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * Contact
 */
abstract class Contact implements UserInterface, EquatableInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"search", "public_api", "private_api"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     * @Groups({"search", "public_api", "private_api"})
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     * @Groups({"search", "public_api", "private_api"})
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Groups({"search", "public_api", "private_api"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Groups({"search", "private_api", "public_api"})
     */
    protected $address;

    /**
     * @var boolean
     *
     * @ORM\Column(name="allow_campaign_mail", type="boolean")
     */
    protected $allowCampaignMail;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     * @Groups({"search", "public_api"})
     */
    protected $phone;

    /**
     * @ManyToMany(targetEntity="\Flower\ModelBundle\Entity\Clients\Account", inversedBy="contacts")
     * @JoinTable(name="account_contact")
     * */
    protected $accounts;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\ContactSource")
     * @JoinColumn(name="contact_source_id", referencedColumnName="id")
     * @Groups({"api","public_api"})
     */
    protected $source;

    /**
     * @var string
     *
     * @ORM\Column(name="observations", type="text", nullable=true)
     * @Groups({"search"})
     */
    protected $observations;

    /**
     * @var integer
     *
     * @ORM\Column(name="email_grade", type="string", length=255, nullable=true)
     * @Groups({"private_api"})
     */
    protected $emailGrade;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * */
    protected $assignee;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
        $this->allowCampaignMail = true;
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
     * Set firstname
     *
     * @param string $firstname
     * @return Contact
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return Contact
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Contact
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Contact
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Contact
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set created
     *
     * @param DateTime $created
     * @return Contact
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param DateTime $updated
     * @return Contact
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    public function getHappyName()
    {
        if (!is_null($this->firstname) && !is_null($this->lastname)) {
            return ucfirst($this->firstname) . " " . ucfirst($this->lastname);
        } elseif (!is_null($this->firstname)) {
            return ucfirst($this->firstname);
        } else {
            return $this->email;
        }
    }

    public function __toString()
    {
        return $this->getHappyName();
    }

    /**
     * Set assignee
     *
     * @param \Flower\ModelBundle\Entity\User\User $assignee
     * @return Contact
     */
    public function setAssignee(\Flower\ModelBundle\Entity\User\User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \Flower\ModelBundle\Entity\User\User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Add accounts
     *
     * @param \Flower\ModelBundle\Entity\Clients\Account $accounts
     * @return Contact
     */
    public function addAccount(\Flower\ModelBundle\Entity\Clients\Account $accounts)
    {
        $this->accounts[] = $accounts;

        return $this;
    }

    /**
     * Remove accounts
     *
     * @param \Flower\ModelBundle\Entity\Clients\Account $accounts
     */
    public function removeAccount(\Flower\ModelBundle\Entity\Clients\Account $accounts)
    {
        $this->accounts->removeElement($accounts);
    }

    /**
     * Get accounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    /**
     * Get observations
     * @return String
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * Set observations
     * @return boolean
     */
    public function setAllowCampaignMail($allowCampaignMail)
    {
        $this->allowCampaignMail = $allowCampaignMail;
    }

    /**
     * Set observations
     * @return String
     */
    public function setObservations($observations)
    {
        $this->observations = $observations;
        return $this;
    }

    /**
     * Get emailGrade
     * @return string
     */
    public function getEmailGrade()
    {
        return $this->emailGrade;
    }

    /**
     * Set emailGrade
     * @return string
     */
    public function setEmailGrade($emailGrade)
    {
        $this->emailGrade = $emailGrade;
        return $this;
    }

    public function getDefaultAccount(){
        return $this->getAccounts()->first();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($user->getUsername() == $this->getEmail()) {
            return true;
        }
        return false;
    }

    public function getRoles()
    {
        return array('ROLE_CLIENT');
    }

    public function getPassword()
    {
        return '$2a$04$7kKdogvCOPp7zvgxntwvduOlon5yYHOUQDLrXS8TwwCyzm8Sn21ca';
    }

    public function getSalt()
    {
        return '';
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials()
    {
        return null;
    }


}
