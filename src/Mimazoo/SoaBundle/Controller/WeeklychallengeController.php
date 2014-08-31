<?php

namespace Mimazoo\SoaBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use FacebookApiException;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

use Mimazoo\SoaBundle\Controller\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\FacebookBundle\Facebook\FacebookSessionPersistence;


use Mimazoo\SoaBundle\Entity\Player;
use Mimazoo\SoaBundle\Entity\Challenge;
use Mimazoo\SoaBundle\Entity\WeeklyChallenge;
use Mimazoo\SoaBundle\Hal\Hal;

use Mimazoo\SoaBundle\Form\Type\PlayerType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Util\Debug;
use Symfony\Component\Translation\Tests\String;
use RMS\PushNotificationsBundle\Message\iOSMessage;


class WeeklychallengeController extends Controller
{


    /**
     * @View(statusCode="200")
     *
     */
    public function cgetAction(Request $request){
        if(true !== ($view = $this->validatePlayerIsSuperUser($request)))
            return $view;

        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:WeeklyChallenge');

        $filter = $request->request->has("filter") ? $request->request->get("filter") : "all";

        $qb = $repository->createQueryBuilder('wc');

        switch($filter){
            case "completed":
            $qb = $qb->where('wc.completedOn IS NOT NULL');
            break;
            case "waiting":
            $qb = $qb->where('wc.startedOn IS NOT NULL');
            break;
            case "live":
            $qb = $qb->where('wc.startedOn IS NOT NULL AND wc.completedOn IS NULL');
            break;
            default:
            break;
        }
        $qb = $qb->orderBy('wc.id', 'ASC');

        $result = $qb->getQuery()->getResult();
        return array('success' => 'true', 'data' => $result);
    }

    /**
     * @View(statusCode="200")
     *
     */
    public function getAction($id, Request $request){
        if(true !== ($view = $this->validatePlayerIsSuperUser($request)))
            return $view;

        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:WeeklyChallenge');

        $wc = $repository->findOneById($id);

        if($wc != null)
            return array('success' => 'true', 'data' => $wc);
        else
            return array('success' => 'false');
    }

    /**
     * @View(statusCode="200")
     *
     */

    public function getCurrentAction(Request $request)
    {
        if(($player = $this->GetPlayerByToken($request)) == false)
            return $this->view("Invalid token", 400);
        
        $challenge = $this->getCurrentChallenge();


        if(($challenge = $this->getCurrentChallenge()) != false)
            return array('success' => 'true', 'data' => array($challenge->toJson()));
        else
            return array('success' => 'true', 'data' => array());
    }

    protected function getCurrentChallenge(){
        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:WeeklyChallenge');

        $qb = $repository->createQueryBuilder('wc');
        $qb->where('wc.isCompleted=false')
           ->orderBy('wc.id', 'ASC')
           ->setMaxResults(1);

        $result = $qb->getQuery()->getResult();
        if(count($result) == 1)
            return $result[0];
        return false;
    }

    /**
     * @View(statusCode="204")
     */
    public function postCurrentCompleteAction(Request $request){
        if(true !== ($view = $this->validatePlayerIsSuperUser($request)))
            return $view;

        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:WeeklyChallenge');

        $qb = $repository->createQueryBuilder('wc');
        $qb->where('wc.isCompleted=false')
           ->orderBy('wc.id', 'ASC')
           ->setMaxResults(1);

        $result = $qb->getQuery()->getResult();

        if(count($result) != 1)
            return $this->view(array("success" => false, "error" => "Current challenge can not be found!"), 400);

        // TODO: Once notifications are in place so this also. 
    }

     /**
     * @View(statusCode="204")
     *
     * Type values:
     * - 1  -> collect most coins across the whole week
     * - 2  -> run the largest distance. Every run adds to previous distance.
     * - 3  -> collect most coins in one game
     * - 4  -> run the furthest in one game
     * - 5  -> kill most enemies in a week.
     * - 6  -> defeat the wolf the fastest
     * - 7  -> catch as many bees in a week as you can.
     * - 8  -> get most bee bonus levels
     * - 9  -> get most running bonus levels
     * - 10 -> max distance not loosing a piggy
     * - 11 -> collect the most powerups in a week
     * - 12 -> collect the most "heart" powerups
     * - 13 + -> specific enemy kill, specific powerup pickup.
     */
    public function postScoreAction(Request $request){
        if(($player = $this->GetPlayerByToken($request)) == false)
            return $this->view("Invalid token", 400);

        $challenge = $this->getCurrentChallenge();
        $type = $challenge->getType();

        if($type == 1)
            $this->processScoreWithStrategy($player, $challenge, intval($request->request->get("coins", 0)), 1);
        else if($type == 2)
            $this->processScoreWithStrategy($player, $challenge, intval($request->request->get("distance", 0)), 1);
        else if($type == 3)
            $this->processScoreWithStrategy($player, intval($request->request->get("enemiesKill", 0)), intval($request->request->get("coins", 0)), 2);
        else if($type == 4)
            $this->processScoreWithStrategy($player, $challenge, intval($request->request->get("distance", 0)), 2);
        else if($type == 5)
            $this->processScoreWithStrategy($player, $challenge, intval($request->request->get("enemiesKill", 0)), 1);
        else if($type == 6)
            $this->processScoreWithStrategy($player, $challenge, floatval($request->request->get("wolfDefeatTime", 10)), 3);
        else if($type == 7)
            $this->processScoreWithStrategy($player, $challenge, intval($request->request->get("beesCaught", 0)), 1);
        else if($type == 8)
            $this->processScoreWithStrategy($player, $challenge, intval($request->request->get("beeBonusLevels", 0)), 1);
        else if($type == 9)
            $this->processScoreWithStrategy($player, $challenge, intval($request->request->get("runBonusLevels", 0)), 1);
        else if($type == 10)
            $this->processScoreWithStrategy($player, $challenge, intval($request->request->get("distancePiggyLost", 0)), 2);
        else if($type == 11)
            $this->processScoreWithStrategy($player, $challenge, intval($request->request->get("powerupsCollected", 0)), 1);
        else if($type == 12)
            $this->processScoreWithStrategy($player, $challenge, intval($request->request->get("powerupsHeartCollected", 0)), 1);


    }

    /**
     * Valid score processing strategies:
     * - 1 -> add to previous value
     * - 2 -> replace previous value if greater
     * - 3 -> replace previous value if smaller
     *
     * returns true on success and false on failure
     */
    protected function processScoreWithStrategy($player, $challenge, $score, $strategy){
        $score = floatval($score); // No sql injections please :)
        $em = $this->getDoctrine()->getManager();
        $sql = "INSERT INTO weekly_challenge_score SET score=" . $score . ", player_id=" . $player->getId() . ", challenge_id=" . $challenge->getId() . ", updated = NOW(), created = NOW() ON DUPLICATE KEY UPDATE ";
        switch ($strategy) {
            case 1:
                $sql .= " score = score + " . $score;
                break;
            case 2:
                $sql .= " score = (case when " . $score . " > score then " . $score . " else score end)";
                break;
            
            case 3:
                $sql .= " score = (case when " . $score . " < score then " . $score . " else score end)";
                break;
            default:
                throw new Exception("Invalid update strategy index: " . $strategy, 1);
                break;
        }
        return $em->getConnection()->prepare($sql)->execute();
    }

    /**
     * @View(statusCode="200")
     */
    public function getTopscoresAction(Request $request){
        if(($player = $this->GetPlayerByToken($request)) == false)
            return $this->view("Invalid token", 400);

        $challenge = $this->getCurrentChallenge();

        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:WeeklyChallengeScore');

        $scoreOrder = $challenge->getSmallerIsBetter() ? 'ASC' : 'DESC';

        $qb = $repository->createQueryBuilder('wcs');
        $qb->orderBy('wcs.score', $scoreOrder)
           ->where('wcs.weeklyChallenge = ' . $challenge->getId())
           ->setMaxResults(20);


        $scores = array();
        $count = 0;
        $playerInResultSet = false;
        foreach($qb->getQuery()->getResult() as $topScore){
            $count++;
            if($topScore->getPlayer()->getId() == $player->getId()){
                $playerInResultSet = true;
            }

            $scores[] = $topScore->toJson($count, $challenge);
        }

        //Player not in top scores list. Find and add him to the results
        if(!$playerInResultSet){
            //Remove last score
            array_pop($scores);
            $q = $this->getDoctrine()->GetConnection($this->getDoctrine()->getDefaultConnectionName());
            /*
             * $var Doctrine\DBAL\Driver\PDOStatement
             */

            // If this starts to underperform, you could estimate the rank by looking at the score, and the score of 20th player.
            $result = $q->query(
                "select *, FIND_IN_SET(score, ( SELECT GROUP_CONCAT( score ORDER BY score $scoreOrder) FROM weekly_challenge_score WHERE challenge_id = " . $challenge->getId() . ")) as rank " . 
                "FROM weekly_challenge_score wc INNER JOIN player p ON p.id = wc.player_id WHERE p.id=" . $player->GetId() . " AND wc.challenge_id = " . $challenge->getId() . ";");
            foreach($result as $value){
                $scores[] = array(
                    'rank' => intval($value['rank']),
                    'score' => strval($challenge->getIsFloat() ? floatval($value['score']) : intval($value['score'])),
                    'description' => $challenge->getDescription(),
                    'player' => array('id' => intval($value['id']),
                                   'name' => $value['name'],
                                   'firstName' => $value['first_name'],
                                   'lastName' => $value['surname'],
                                   'fb_id' => $value['fb_id'],
                                   'distance' => intval($value['distance_best']),
                        )
                    );
                //should never be more than 1 result
                break;
            }
        }

        return array('success' => 'true', 'data' => $scores);
        // score is always float, but sometimes it wants to be represented in int value
    }

    /**
     * @View(statusCode="204")
     */
    public function postAction(Request $request){
        if(true !== ($view = $this->validatePlayerIsSuperUser($request)))
            return $view;

        $wc = new WeeklyChallenge();
        $wc->setDescription($request->get("description"));
        $wc->setType($request->get("type"));

        return $this->process($wc);
    }

    /**
     * Update item
     *
     * @param WeeklyChallenge
     *
     */
    protected function process( WeeklyChallenge $challenge) {
        $new = (NULL === $challenge->getId())?true:false;

        $validator = $this->get('validator');
        $errors = $validator->validate($challenge);

        if (count($errors) > 0) {
            return $this->view($errors, 400);
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->persist($challenge);
            try {
                $em->flush();
            } catch (DBALException  $e) {
                //print_r($e->getMessage());die('foo');
                return $this->view($e->getMessage(), 400);
            }
        }
    }
}
