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
 * @ORM\Table(indexes={@ORM\Index(name="distance_idx", columns={"distance_best"})})
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
     * @ORM\Column(type="string", length=64, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min = "1",
     *                max = "50",
     *                minMessage = "Facebook id must be at least {{ limit }} characters long",
     *                maxMessage = "Facebook id must be less then {{ limit }} characters long")
     * @Groups({"always"})
     */
    protected $fbId;
    
    /**
     * @var string $fbAccessToken
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(min = "10",
     *                max = "255",
     *                minMessage = "Facebook token must be at least {{ limit }} characters long",
     *                maxMessage = "Facebook token must be less then {{ limit }} characters long")
     * @Assert\NotBlank()
     * @Groups({"always"})
     */
    protected $fbAccessToken;
    
    /**
     * @var string $applePushToken
     *
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Groups({"always"})
     */
    protected $applePushToken;
    
    /**
     * @var string $name
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = "1",
     *                max = "255",
     *                minMessage = "Name must be at least {{ limit }} characters long",
     *                maxMessage = "Name must be less then {{ limit }} characters long")
     * @Assert\NotBlank()
     * @Groups({"always"})
     */
    protected $name;

    /**
     * @var string $firstName
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"always"})
     */
    protected $firstName;


    /**
     * @var string $surname
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"always"})
     */
    protected $surname;
    
    
    /**
     * @var string $slug
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"always"})
     */
    protected $slug;
    
    /**
     * @var integer $distanceBest
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(
     *               min = -1,
     *               max = 350000,
     *               minMessage = "Your distance should be a positive number or -1",
     *               maxMessage = "You ran more then 350000 meters? Who are you kidding?"
     * )
     * @Groups({"always"})
     */
    protected $distanceBest;

    /**
     * @var integer $present_selected
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(
     *               min = 0,
     *               max = 15,
     *               minMessage = "Selected present should be a positive number",
     *               maxMessage = "Value {{ value }} for selected present is too big"
     * )
     * @Groups({"always"})
     */
    protected $present_selected;

    /**
     * @ORM\ManyToMany(targetEntity="Player")
     * @Assert\Valid(traverse=false)
     * @ORM\JoinTable(name="friends",
     *      joinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="other_id", referencedColumnName="id")}
     *      )
     */
    protected $friends;

    /**
     * Get friends
     *
     * @return ArrayCollection
     */
    public function getFriends(){
        return $this->friends;
    }

    /**
     * Set friends
     *
     * @param ArrayCollection $friends
     * @return Player
     */
    public function setFriends(ArrayCollection $friends){
        $this->friends = $friends;
        return $this;
    }

    public function __construct() {
        $this->friends = new ArrayCollection();
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
     * @param string $firstName
     * @return Player
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
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
     * Set surname
     *
     * @param string $surname
     * @return Player
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
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
     * Add friends
     *
     * @param \Mimazoo\SoaBundle\Entity\Player $friends
     * @return Player
     */
    public function addFriend(\Mimazoo\SoaBundle\Entity\Player $friends)
    {
        $this->friends[] = $friends;

        return $this;
    }

    /**
     * Remove friends
     *
     * @param \Mimazoo\SoaBundle\Entity\Player $friends
     */
    public function removeFriend(\Mimazoo\SoaBundle\Entity\Player $friends)
    {
        $this->friends->removeElement($friends);
    }

    public function toJson($isPublicView = false, $showFriends = true, $includedRank = -1){
        $player = array('id' => $this->getId(),
                        'name' => $this->getName(),
                        'firstName' => $this->getFirstName(),
                        'lastName' => $this->getSurname(),
                        'fb_id' => $this->getFbId(),
                        'present_id' => $this->getPresentSelected(),
                        'distance' => $this->getDistanceBest()
        );

        if(!$isPublicView){
            $player['apple_push_token'] = $this->getApplePushToken();
        }

        if($includedRank != -1)
            $player['rank'] = $includedRank;

        if($showFriends){
            $friends = array();
            foreach($this->getFriends() as $friend){
                $friends[] = array('id' => $friend->getId(),
                    'name' => $friend->getName(),
                    'firstName' => $friend->getFirstName(),
                    'lastName' => $friend->getSurname(),
                    'fb_id' => $friend->getFbId(),
                    'present_id' => $friend->getPresentSelected(),
                    'distance' => $friend->getDistanceBest()
                );
            }

            $player['friends'] = $friends;
        }


        return $player;
    }


    /**
     * Set present_selected
     *
     * @param integer $presentSelected
     * @return Player
     */
    public function setPresentSelected($presentSelected)
    {
        $this->present_selected = $presentSelected;

        return $this;
    }

    /**
     * Get present_selected
     *
     * @return integer 
     */
    public function getPresentSelected()
    {
        return $this->present_selected;
    }
}
