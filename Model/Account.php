<?php

namespace Flower\ClientsBundle\Model;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\OneToMany;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;
use Flower\ModelBundle\Entity\User\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Account
 *
 * @UniqueEntity(fields={"cuit"})
 */
abstract class Account
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"search", "public_api", "api"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"search", "public_api", "api"})
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="businessName", type="string", length=255)
     * @Groups({"search", "public_api", "api"})
     * @Assert\NotBlank()
     */
    protected $businessName;
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     * @Groups({"search", "public_api", "api"})
     */
    protected $phone;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\BillingType")
     * @JoinColumn(name="billingtype_id", referencedColumnName="id")
     * */
    protected $billingType;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\AccountStatus")
     * @JoinColumn(name="accountstatus_id", referencedColumnName="id")
     * */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Groups({"search", "public_api", "api"})
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=255, nullable=true)
     * @Groups({"search", "public_api", "api"})
     */
    protected $cuit;

    /**
     * @ManyToMany(targetEntity="\Flower\ModelBundle\Entity\Clients\Contact", mappedBy="accounts")
     */
    protected $contacts;

    /**
     * @ORM\OneToMany(targetEntity="\Flower\ModelBundle\Entity\Clients\Subsidiary", mappedBy="account")
     */
    protected $subsidiaries;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * @Groups({"api","public_api"})
     */
    protected $assignee;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\Activity")
     * @JoinColumn(name="activity_id", referencedColumnName="id")
     * @Groups({"api","public_api"})
     */
    protected $activity;

    /**
     * @ManyToMany(targetEntity="\Flower\ModelBundle\Entity\Board\Board")
     * @JoinTable(name="accounts_boards",
     *      joinColumns={@JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="board_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $boards;

    /**
     * @ManyToMany(targetEntity="\Flower\ModelBundle\Entity\User\SecurityGroup")
     * @JoinTable(name="account_user_security_groups",
     *      joinColumns={@JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="security_group_id", referencedColumnName="id")}
     *      )
     */
    protected $securityGroups;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;
    /**
     * @OneToMany(targetEntity="\Flower\ModelBundle\Entity\Clients\Note", mappedBy="account")
     * */
    protected $notes;


    /**
     * @ORM\OneToMany(targetEntity="\Flower\ModelBundle\Entity\Clients\CallEvent", mappedBy="account")
     */
    protected $calls;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->boards = new ArrayCollection();
        $this->securityGroups = new ArrayCollection();
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
     * @return Account
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
     * Get businessName
     * @return string
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set businessName
     * @return string
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;
        return $this;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Account
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
     * Set address
     *
     * @param string $address
     * @return Account
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
     * Set created
     *
     * @param DateTime $created
     * @return Account
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
     * @return Account
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


    public function __toString()
    {
        return "#" . $this->id . " " . $this->name;
    }

    /**
     * Set assignee
     *
     * @param \Flower\ModelBundle\Entity\User\User $assignee
     * @return Account
     */
    public function setAssignee(\Flower\ModelBundle\Entity\User\User $assignee)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     * @return \Flower\ModelBundle\Entity\User\User $assignee
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     * @return Account
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return string
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Add notes
     *
     * @param \Flower\ModelBundle\Entity\Clients\Note $notes
     * @return Account
     */
    public function addNote(\Flower\ModelBundle\Entity\Clients\Note $notes)
    {
        $this->notes[] = $notes;

        return $this;
    }

    /**
     * Remove notes
     *
     * @param \Flower\ModelBundle\Entity\Clients\Note $notes
     */
    public function removeNote(\Flower\ModelBundle\Entity\Clients\Note $notes)
    {
        $this->notes->removeElement($notes);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Add contacts
     *
     * @param \Flower\ModelBundle\Entity\Clients\Contact $contacts
     * @return Account
     */
    public function addContact(\Flower\ModelBundle\Entity\Clients\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \Flower\ModelBundle\Entity\Clients\Contact $contacts
     */
    public function removeContact(\Flower\ModelBundle\Entity\Clients\Contact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }


    /**
     * Add boards
     *
     * @param \Flower\ModelBundle\Entity\Board\Board $boards
     * @return Account
     */
    public function addBoard(\Flower\ModelBundle\Entity\Board\Board $boards)
    {
        $this->boards[] = $boards;

        return $this;
    }

    /**
     * Remove boards
     *
     * @param \Flower\ModelBundle\Entity\Board\Board $boards
     */
    public function removeBoard(\Flower\ModelBundle\Entity\Board\Board $boards)
    {
        $this->boards->removeElement($boards);
    }

    /**
     * Get boards
     *
     * @return Collection
     */
    public function getBoards()
    {
        return $this->boards;
    }

    /**
     * Set activity
     *
     * @param \Flower\ModelBundle\Entity\Clients\Activity $activity
     * @return Account
     */
    public function setActivity(\Flower\ModelBundle\Entity\Clients\Activity $activity = null)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return \Flower\ModelBundle\Entity\Clients\Activity
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Add calls
     *
     * @param \Flower\ModelBundle\Entity\Clients\CallEvent $calls
     * @return Account
     */
    public function addCall(\Flower\ModelBundle\Entity\Clients\CallEvent $calls)
    {
        $this->calls[] = $calls;

        return $this;
    }

    /**
     * Remove calls
     *
     * @param \Flower\ModelBundle\Entity\Clients\CallEvent $calls
     */
    public function removeCall(\Flower\ModelBundle\Entity\Clients\CallEvent $calls)
    {
        $this->calls->removeElement($calls);
    }

    /**
     * Get calls
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * Get billingType
     * @return
     */
    public function getBillingType()
    {
        return $this->billingType;
    }

    /**
     * Set billingType
     * @return
     */
    public function setBillingType($billingType)
    {
        $this->billingType = $billingType;
        return $this;
    }

    /**
     * Get status
     * @return
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     * @return
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Add subsidiaries
     *
     * @param \Flower\ModelBundle\Entity\Clients\Subsidiary $subsidiaries
     * @return Account
     */
    public function addSubsidiary(\Flower\ModelBundle\Entity\Clients\Subsidiary $subsidiaries)
    {
        $this->subsidiaries[] = $subsidiaries;

        return $this;
    }

    /**
     * Remove subsidiaries
     *
     * @param \Flower\ModelBundle\Entity\Clients\Subsidiary $subsidiaries
     */
    public function removeSubsidiary(\Flower\ModelBundle\Entity\Clients\Subsidiary $subsidiaries)
    {
        $this->subsidiaries->removeElement($subsidiaries);
    }

    /**
     * Get subsidiaries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubsidiaries()
    {
        return $this->subsidiaries;
    }

    /**
     * Add securityGroup
     *
     * @param \Flower\ModelBundle\Entity\User\SecurityGroup $securityGroups
     * @return \Flower\ModelBundle\Entity\Clients\Activity
     */
    public function addSecurityGroup(\Flower\ModelBundle\Entity\User\SecurityGroup $securityGroups)
    {
        $this->securityGroups[] = $securityGroups;

        return $this;
    }

    /**
     * Remove securityGroups
     *
     * @param \Flower\ModelBundle\Entity\User\SecurityGroup $securityGroups
     */
    public function removeSecurityGroup(\Flower\ModelBundle\Entity\User\SecurityGroup $securityGroups)
    {
        $this->securityGroups->removeElement($securityGroups);
    }

    /**
     * Get securityGroups
     *
     * @return Collection
     */
    public function getSecurityGroups()
    {
        return $this->securityGroups;
    }

}
