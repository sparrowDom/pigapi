<?php

namespace Mimazoo\SoaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Mimazoo\SoaBundle\Validator\Constraints as MimazooAssert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;

/**
 * Mimazoo\SoaBundle\Entity\Notification
 *
 * @ORM\Entity
 */
class Notification extends BaseAuditableEntity
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
     * @var string $applePushToken
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"always"})
     */
    protected $applePushToken;

    /**
     * @var string $message
     *
     * @ORM\Column(type="string", length=160)
     * @Assert\Length(min = "1",
     *                max = "160",
     *                minMessage = "APN message should be at least {{ limit }} characters long",
     *                maxMessage = "APN message must be less than {{ limit }} characters long")
     * @Groups({"always"})
     */
    protected $message;


    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @Assert\NotBlank()
     * @Assert\Valid(traverse=false)
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    protected $player;

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
     * Set applePushToken
     *
     * @param string $applePushToken
     * @return Player
     */
    public function setApplePushToken($applePushToken)
    {
        $this->applePushToken = $applePushToken;

        return $this;
    }

    /**
     * Get applePushToken
     *
     * @return string
     */
    public function getApplePushToken()
    {
        return $this->applePushToken;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Notification
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set player
     *
     * @param \Mimazoo\SoaBundle\Entity\Player $player
     * @return Notification
     */
    public function setPlayer(\Mimazoo\SoaBundle\Entity\Player $player = null)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return \Mimazoo\SoaBundle\Entity\Player 
     */
    public function getPlayer()
    {
        return $this->player;
    }
}
