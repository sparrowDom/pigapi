<?php

namespace Mimazoo\SoaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Mimazoo\SoaBundle\Validator\Constraints as MimazooAssert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;

/**
 * Mimazoo\SoaBundle\Entity\Challenge
 *
 * @ORM\Entity
 */
class WeeklyChallenge extends BaseAuditableEntity
{

    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"always"})
     */
    protected $id;

    /**
     * @var string $description
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = "1",
     *                max = "255",
     *                minMessage = "Name must be at least {{ limit }} characters long",
     *                maxMessage = "Name must be less then {{ limit }} characters long")
     * @Groups({"always"})
     */
    protected $description;

    /**
     * @var boolean $isCompleted
     *
     * @ORM\Column(type="boolean")
     * @Groups({"always"})
     */
    protected $isCompleted = false;
    
    /**
     * @var \DateTime $startedOn
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"always"})
     */
    protected $startedOn = null;

    /**
     * @var \DateTime $completedOn
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"always"})
     */
    protected $completedOn = null;


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
     * Set type
     *
     * @param integer $type
     * @return WeeklyChallenge
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return WeeklyChallenge
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
     * Set isCompleted
     *
     * @param boolean $isCompleted
     * @return WeeklyChallenge
     */
    public function setIsCompleted($isCompleted)
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    /**
     * Get isCompleted
     *
     * @return boolean 
     */
    public function getIsCompleted()
    {
        return $this->isCompleted;
    }

    public function toJson(){
        $challenge = array(
            'id' => $this->getId(),
            'description' => $this->getDescription()
        );

        return $challenge;
    }



    /**
     * Set startedOn
     *
     * @param \DateTime $startedOn
     * @return WeeklyChallenge
     */
    public function setStartedOn($startedOn)
    {
        $this->startedOn = $startedOn;
        return $this;
    }

    /**
     * Get startedOn
     *
     * @return \DateTime 
     */
    public function getStartedOn()
    {
        return $this->startedOn;
    }

    /**
     * Set completedOn
     *
     * @param \DateTime $completedOn
     * @return WeeklyChallenge
     */
    public function setCompletedOn($completedOn)
    {
        $this->completedOn = $completedOn;
        return $this;
    }

    /**
     * Get completedOn
     *
     * @return \DateTime 
     */
    public function getCompletedOn()
    {
        return $this->completedOn;
    }
}
