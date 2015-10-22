<?php

namespace Flower\ClientsBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
/**
 * 
 */
abstract class CallEvent
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    protected $date;

    /**
     * @var string
     *
     * @ORM\Column(name="contactName", type="string", length=255, nullable=true)
     */
    protected $contactName;

    /**
     * @var integer
     *
     * @ORM\Column(name="frequency", type="smallint")
     */
    protected $frequency;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\CallEventStatus")
     * @JoinColumn(name="status_id", referencedColumnName="id")
     * */
    protected $status;
    

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\Account", inversedBy="calls")
     * @JoinColumn(name="account_id", referencedColumnName="id")
     **/
    protected $account;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * */
    protected $assignee;
    
    public function __construct(){
        $this->contactName = "";
        $this->frequency = 0;
    }

    public function __toString()
    {
        return $this->subject;
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
     * Set date
     *
     * @param \DateTime $date
     * @return CallEvent
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set contactName
     *
     * @param string $contactName
     * @return CallEvent
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get contactName
     *
     * @return string 
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set frequency
     *
     * @param integer $frequency
     * @return CallEvent
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * Get frequency
     *
     * @return integer 
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return CallEvent
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return CallEvent
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return CallEvent
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
        /**
     * Set account
     *
     * @param \Flower\ModelBundle\Entity\Clients\Account $account
     * @return CallEvent
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

    /**
     * Set assignee
     *
     * @param \Flower\ModelBundle\Entity\User\User $assignee
     * @return CallEvent
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
     * Set status
     *
     * @param \Flower\ModelBundle\Entity\Clients\CallEventStatus $status
     * @return CallEvent
     */
    public function setCallEventSatus(\Flower\ModelBundle\Entity\Clients\CallEventStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Flower\ModelBundle\Entity\Clients\CallEventStatus 
     */
    public function getCallEventSatus()
    {
        return $this->status;
    }
}
