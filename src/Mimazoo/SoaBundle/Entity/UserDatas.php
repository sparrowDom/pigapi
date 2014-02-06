<?php
namespace Mimazoo\SoaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Mimazoo\SoaBundle\Validator\Constraints as MimazooAssert;
use Gedmo\Mapping\Annotation as Gedmo;
use Mimazoo\SoaBundle\Entity\BaseAuditableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;

/**
 * Mimazoo\SoaBundle\Entity\UserDatas
 *
 * @ORM\Entity
 * @ORM\Table(indexes={@ORM\Index(name="user_id_idx", columns={"user_id"})})
 */
class UserDatas extends BaseAuditableEntity
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
     * @var integer $distanceBest
     *
     * @ORM\Column(type="integer", nullable=true)
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
     * @var integer $storyProgress
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *               min = -1,
     *               max = 50,
     *               minMessage = "Your story progress should be a positive number or -1",
     *               maxMessage = "Your story progress value is too high."
     * )
     * @Groups({"always"})
     */
    protected $storyProgress;

    /**
     * @var integer $coins
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *               min = 0,
     *               max = 50000,
     *               minMessage = "Can not acquire negative coins",
     *               maxMessage = "Probably you have acquired too much coins."
     * )
     * @Groups({"always"})
     */
    protected $coins;

    /**
     * @var integer $gamesPlayed
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *               min = 0,
     *               max = 100000,
     *               minMessage = "Can not acquire negative games played. Our game does not suck that much!",
     *               maxMessage = "Wow you just got crazy with the number of times you are playing the game!"
     * )
     * @Groups({"always"})
     */
    protected $gamesPlayed;

    /**
     * @var integer $userId
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"always"})
     */
    protected $userId;

    /**
     * @var string $applePushToken
     *
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * Set distanceBest
     *
     * @param integer $distanceBest
     * @return UserDatas
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
     * Set storyProgress
     *
     * @param integer $storyProgress
     * @return UserDatas
     */
    public function setStoryProgress($storyProgress)
    {
        $this->storyProgress = $storyProgress;

        return $this;
    }

    /**
     * Get storyProgress
     *
     * @return integer 
     */
    public function getStoryProgress()
    {
        return $this->storyProgress;
    }

    /**
     * Set coins
     *
     * @param integer $coins
     * @return UserDatas
     */
    public function setCoins($coins)
    {
        $this->coins = $coins;

        return $this;
    }

    /**
     * Get coins
     *
     * @return integer 
     */
    public function getCoins()
    {
        return $this->coins;
    }

    /**
     * Set gamesPlayed
     *
     * @param integer $gamesPlayed
     * @return UserDatas
     */
    public function setGamesPlayed($gamesPlayed)
    {
        $this->gamesPlayed = $gamesPlayed;

        return $this;
    }

    /**
     * Get gamesPlayed
     *
     * @return integer 
     */
    public function getGamesPlayed()
    {
        return $this->gamesPlayed;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserDatas
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set applePushToken
     *
     * @param string $applePushToken
     * @return UserDatas
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
}
