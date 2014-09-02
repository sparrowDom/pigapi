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
 * @ORM\Table(indexes={
 *  @ORM\Index(name="weekly_challenge_id_idx", columns={"challenge_id"}),
 *  @ORM\Index(name="weekly_challenge_score_id_idx", columns={"challenge_id", "score"}),
 *  @ORM\Index(name="weekly_challenge_score_idx", columns={"score"})
 *  }, uniqueConstraints={@ORM\UniqueConstraint(name="weekly_challenge_player_challenge_id", columns={"player_id", "challenge_id"})}
 * )
 */
class WeeklyChallengeScore extends BaseAuditableEntity
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
     * @var float $score
     *
     * @ORM\Column(type="float", nullable=false)
     * @Groups({"always"})
     */
    protected $score;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @Assert\NotBlank()
     * @Assert\Valid(traverse=false)
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    protected $player;

    /**
     * @ORM\ManyToOne(targetEntity="WeeklyChallenge")
     * @Assert\NotBlank()
     * @Assert\Valid(traverse=false)
     * @ORM\JoinColumn(name="challenge_id", referencedColumnName="id")
     */
    protected $weeklyChallenge;

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
     * Set score
     *
     * @param integer $score
     * @return WeeklyChallengeScore
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set player
     *
     * @param \Mimazoo\SoaBundle\Entity\Player $player
     * @return WeeklyChallengeScore
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

    /**
     * Set weeklyChallenge
     *
     * @param \Mimazoo\SoaBundle\Entity\WeeklyChallenge $weeklyChallenge
     * @return WeeklyChallengeScore
     */
    public function setWeeklyChallenge(\Mimazoo\SoaBundle\Entity\WeeklyChallenge $weeklyChallenge = null)
    {
        $this->weeklyChallenge = $weeklyChallenge;

        return $this;
    }

    /**
     * Get weeklyChallenge
     *
     * @return \Mimazoo\SoaBundle\Entity\WeeklyChallenge 
     */
    public function getWeeklyChallenge()
    {
        return $this->weeklyChallenge;
    }

    public function toJson($count, $challenge){

        return array('rank' => $count,
            'score' => $challenge->getIsFloat() ? floatval($this->getScore()) : intval($this->getScore()),
            'description' => $challenge->getDescription(),
            'player' => $this->getPlayer()->toJson(true, false)
        );
    }
}
