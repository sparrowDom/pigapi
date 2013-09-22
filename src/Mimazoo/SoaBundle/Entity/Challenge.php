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
     *
     * @var integer $state
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(
     *               min = 0,
     *               max = 3,
     *               minMessage = "Selected present should be a positive number",
     *               maxMessage = "Value {{ value }} for selected present is too big"
     * )
     * @Groups({"always"})
     */
    protected $state;



    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @Assert\Valid(traverse=false)
     * @ORM\JoinColumn(name="challenger_id", referencedColumnName="id"
     *      )
     */
    protected $challenger_id;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @Assert\Valid(traverse=false)
     * @ORM\JoinColumn(name="challenged_id", referencedColumnName="id"
     *      )
     */
    protected $challenged_id;


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
            'challenger_id' => $this->getChallengerId(),
            'challenged_id' => $this->getChallengedId(),
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
     * Set challenger_id
     *
     * @param \Mimazoo\SoaBundle\Entity\Player $challengerId
     * @return Challenge
     */
    public function setChallengerId(\Mimazoo\SoaBundle\Entity\Player $challengerId = null)
    {
        $this->challenger_id = $challengerId;

        return $this;
    }

    /**
     * Get challenger_id
     *
     * @return \Mimazoo\SoaBundle\Entity\Player 
     */
    public function getChallengerId()
    {
        return $this->challenger_id;
    }

    /**
     * Set challenged_id
     *
     * @param \Mimazoo\SoaBundle\Entity\Player $challengedId
     * @return Challenge
     */
    public function setChallengedId(\Mimazoo\SoaBundle\Entity\Player $challengedId = null)
    {
        $this->challenged_id = $challengedId;

        return $this;
    }

    /**
     * Get challenged_id
     *
     * @return \Mimazoo\SoaBundle\Entity\Player 
     */
    public function getChallengedId()
    {
        return $this->challenged_id;
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
