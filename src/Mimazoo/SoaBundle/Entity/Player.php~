<?php

namespace Mimazoo\SoaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Mimazoo\SoaBundle\Validator\Constraints as MimazooAssert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;

/**
 * Mimazoo\SoaBundle\Entity\Player
 *
 * @ORM\Entity
 */
class Player extends BaseAuditableEntity
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
     * @var string $fbId
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"always"})
     */
    protected $fbId;
    
    /**
     * @var string $fbAccessToken
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"always"})
     */
    protected $fbAccessToken;
    
    /**
     * @var string $applePushToken
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"always"})
     */
    protected $applePushToken;
    
    /**
     * @var string $name
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"always"})
     */
    protected $name;
    
    
    /**
     * @var string $slug
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"name"})
     * @Groups({"always"})
     */
    protected $slug;
    
    /**
     * @var integer $distanceBest
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Groups({"always"})
     */
    protected $distanceBest;
    
    /**
     * @var boolean $challengesCounter
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Groups({"always"})
     */
    protected $challengesCounter;


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
     * Set fbId
     *
     * @param string $fbId
     * @return Player
     */
    public function setFbId($fbId)
    {
        $this->fbId = $fbId;

        return $this;
    }

    /**
     * Get fbId
     *
     * @return string 
     */
    public function getFbId()
    {
        return $this->fbId;
    }

    /**
     * Set fbAccessToken
     *
     * @param string $fbAccessToken
     * @return Player
     */
    public function setFbAccessToken($fbAccessToken)
    {
        $this->fbAccessToken = $fbAccessToken;

        return $this;
    }

    /**
     * Get fbAccessToken
     *
     * @return string 
     */
    public function getFbAccessToken()
    {
        return $this->fbAccessToken;
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
     * Set name
     *
     * @param string $name
     * @return Player
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
     * Set slug
     *
     * @param string $slug
     * @return Player
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set distanceBest
     *
     * @param integer $distanceBest
     * @return Player
     */
    public function setDistanceBest($distanceBest)
    {
        $this->distanceBest = $distanceBest;

        return $this;
    }

    /**
     * Get distanceBest
     *
     * @return integer 
     */
    public function getDistanceBest()
    {
        return $this->distanceBest;
    }

    /**
     * Set challengesCounter
     *
     * @param integer $challengesCounter
     * @return Player
     */
    public function setChallengesCounter($challengesCounter)
    {
        $this->challengesCounter = $challengesCounter;

        return $this;
    }

    /**
     * Get challengesCounter
     *
     * @return integer 
     */
    public function getChallengesCounter()
    {
        return $this->challengesCounter;
    }
}
