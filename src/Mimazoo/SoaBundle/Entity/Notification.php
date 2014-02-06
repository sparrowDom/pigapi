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
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Groups({"always"})
     */
    protected $applePushToken;


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

    public function toJson(){
        $notification = array('id' => $this->getId(),
            'apple_token' => $this->getApplePushToken()
        );

        return $notification;
    }
}
