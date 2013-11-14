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
class Challenge extends BaseAuditableEntity
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
     * @var integer $reward
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(
     *               min = 0,
     *               max = 5000,
     *               minMessage = "Selected present should be a positive number",
     *               maxMessage = "Value {{ value }} for selected present is too big"
     * )
     * @Groups({"always"})
     */
    protected $reward;


    /**
     *
     * 0 means coins
     * 1 means distance
     *
     * @var integer $type
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(
     *               min = 0,
     *               max = 5,
     *               minMessage = "Selected present should be a positive number",
     *               maxMessage = "Value {{ value }} for selected present is too big"
     * )
     * @Groups({"always"})
     */
    protected $type;

    /**
     * 0 means challenge still open-fresh
     * 1 means challenge received by the challenged
     * 2 means challenge won by challenger
     * 3 means challenge won by challenged
     * 4 means challenge received by the challenger
     *
     * @var integer $state
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(
     *               min = 0,
     *               max = 4,
     *               minMessage = "Selected present should be a positive number",
     *               maxMessage = "Value {{ value }} for selected present is too big"
     * )
     * @Groups({"always"})
     */
    protected $state;



    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @Assert\NotBlank()
     * @Assert\Valid(traverse=false)
     * @ORM\JoinColumn(name="challenger_id", referencedColumnName="id"
     *      )
     */
    protected $challengerPlayer;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @Assert\NotBlank()
     * @Assert\Valid(traverse=false)
     * @ORM\JoinColumn(name="challenged_id", referencedColumnName="id"
     *      )
     */
    protected $challengedPlayer;


    /**
     * @var integer $value
     *
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     * @Assert\Range(
     *               min = 0,
     *               minMessage = "Selected value should be a positive number"
     * )
     * @Groups({"always"})
     */
    protected $value;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function toJson(){

        return array('id' => $this->getId(),
            'challenged' => $this->getChallengedPlayer()->toJson(true),
            'challenger' => $this->getChallengerPlayer()->toJson(true),
            'state' => $this->getState(),
            'type' => $this->getType(),
            'value' => $this->getValue(),
            'reward' => $this->getReward()
        );
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Challenge
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
     * Set challengerPlayer
     *
     * @param \Mimazoo\SoaBundle\Entity\Player $challengerPlayer
     * @return Challenge
     */
    public function setChallengerPlayer(\Mimazoo\SoaBundle\Entity\Player $challengerPlayer = null)
    {
        $this->challengerPlayer = $challengerPlayer;

        return $this;
    }

    /**
     * Get challenger_id
     *
     * @return \Mimazoo\SoaBundle\Entity\Player 
     */
    public function getChallengerPlayer()
    {
        return $this->challengerPlayer;
    }

    /**
     * Set challengedPlayer
     *
     * @param \Mimazoo\SoaBundle\Entity\Player $challengedPlayer
     * @return Challenge
     */
    public function setChallengedPlayer(\Mimazoo\SoaBundle\Entity\Player $challengedPlayer = null)
    {
        $this->challengedPlayer = $challengedPlayer;

        return $this;
    }

    /**
     * Get challenged_id
     *
     * @return \Mimazoo\SoaBundle\Entity\Player 
     */
    public function getChallengedPlayer()
    {
        return $this->challengedPlayer;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Challenge
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set value
     *
     * @param float $value
     * @return Challenge
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set reward
     *
     * @param integer $reward
     * @return Challenge
     */
    public function setReward($reward)
    {
        $this->reward = $reward;

        return $this;
    }

    /**
     * Get reward
     *
     * @return integer 
     */
    public function getReward()
    {
        return $this->reward;
    }
}
