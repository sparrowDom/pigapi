<?php
namespace Mimazoo\SoaBundle\Service;

use Symfony\Component\Validator\Validator;
use Symfony\Bridge\Monolog\Logger;
use Doctrine\ORM\EntityManager;
use Mimazoo\SoaBundle\Entity\Challenge;
use Mimazoo\SoaBundle\Entity\WeeklyChallenge;

class WeeklyChallengeService
{
    protected $em;
    protected $shared;
    protected $validator;
    protected $logger;

    public function __construct(EntityManager $em, SharedService $shared, Validator $validator, Logger $logger) {
        $this->em = $em;
        $this->shared = $shared;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @View(statusCode="204")
     */
    public function completeChallenge(){
        /*if(true !== ($view = $this->validatePlayerIsSuperUser($request)))
            return $view;*/

        $repository = $this->em
            ->getRepository('MimazooSoaBundle:WeeklyChallenge');

        // Current challenge is found complete it and send notifications
        if(($oldChallenge = $this->getCurrentChallenge()) !== false){
            $oldChallenge->setCompletedOn(new \DateTime("now"));

            $this->notifyChallengeWinner($oldChallenge);

            $this->process($oldChallenge);
            $this->logger->info("Challenge id: " . $oldChallenge->getId() . " text: \"" . $oldChallenge->getDescription() . "\" completed." , get_defined_vars());
        }

        if(($currentChallenge = $this->getCurrentChallenge(true)) == false){
        	$this->logger->error("Another challenge is not ready! Create a new one!");
        	return "Another challenge is not ready! Create a new one!";
            /*return $this->view(array("success" => false, "error" => "Another challenge is not ready! Create a new one!"), 400);*/
        }

        $currentChallenge->setStartedOn(new \DateTime("now"));
        
        $this->notifyNewChallenge($currentChallenge);

        $this->logger->info("Challenge id: " . $currentChallenge->getId() . " text: \"" . $currentChallenge->getDescription() . "\" started." , get_defined_vars());

        //TODO send sms/mail
        $result = $this->process($currentChallenge);
        return ($result == false ? "New challenge started" : $result);
    }

    // WARNING (!) YOU ALTER THIS CODE, ALSO ALTER IT IN WeeklyChallengeController.php
    public function getCurrentChallenge($ignoreStartedOn = false){
        $repository = $this->em
             ->getRepository('MimazooSoaBundle:WeeklyChallenge');

        $qb = $repository->createQueryBuilder('wc');
        $qb->where('wc.completedOn IS NULL')
           ->orderBy('wc.id', 'ASC')
           ->setMaxResults(1);

        if($ignoreStartedOn == true)
            $qb->andWhere('wc.startedOn IS NULL');

        $result = $qb->getQuery()->getResult();
        if(count($result) == 1)
            return $result[0];
        return false;
    }
    
    public function process( WeeklyChallenge $challenge) {
        $new = (NULL === $challenge->getId())?true:false;

        $validator = $this->validator;
        $errors = $validator->validate($challenge);

        if (count($errors) > 0) {
            return $this->view($errors, 400);
        } else {
            $this->em->persist($challenge);
            try {
                $this->em->flush();
            } catch (DBALException  $e) {
                //print_r($e->getMessage());die('foo');
                //return $this->view($e->getMessage(), 400);
                $this->logger->info("Can not save challenge:  " . $e->getMessage(), get_defined_vars());
            }
        }
    }

    public function notifyChallengeWinner($weeklyChallenge) {
        $winner = $this->getWeeklyChallengeWinner($weeklyChallenge);
        if($winner != false)
            $this->shared->sendMessageToAllPlayers("Oink! Winner of Weekly Challenge is " . $winner->getName() . ". Congratulations!");
    }

    public function notifyNewChallenge($wc) {
       $this->shared->sendMessageToAllPlayers("Oink! New Weekly Challenge has just started, try to compete for the High Scores!");
    }

    // False if no players playing
    public function getWeeklyChallengeWinner($weeklyChallenge){
        $repository = $this->em
            ->getRepository('MimazooSoaBundle:WeeklyChallengeScore');

        $qb = $repository->createQueryBuilder('wcs');
        $qb->where('wcs.weeklyChallenge = ' . $weeklyChallenge->getId())
           ->orderBy('wcs.score', $weeklyChallenge->getSmallerIsBetter() ? 'ASC' : 'DESC')
           ->setMaxResults(1);

        $result = $qb->getQuery()->getResult();
        if(count($result) == 1)
            return $result[0]->getPlayer();
        return false;
    }
}