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
use Mimazoo\SoaBundle\Hal\Hal;

use Mimazoo\SoaBundle\Form\Type\PlayerType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Util\Debug;
use Symfony\Component\Translation\Tests\String;
use RMS\PushNotificationsBundle\Message\iOSMessage;


class ChallengeController extends Controller
{

    /**
     * @View(statusCode="200")
     *
     */

    public function cgetAction(Request $request)
    {
        $player = $this->GetPlayerByToken($request);

        $activeChallenges = $this->GetActiveChallenges($player);

        $result = array();

        foreach($activeChallenges as $challenge)
            $result[] = $challenge->toJson();

        return array('success' => 'true', 'data' => $result);
    }

    /**
     * @View(statusCode="200")
     *
     */

    public function getAction($id, Request $request)
    {
        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:Challenge');


        $challenge = $repository->findOneById($id);

        $player = $this->GetPlayerByToken($request);

        if($player == null){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Token invalid'), 400);
        }
        if($challenge == null){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Requested challenge does not exist'), 400);
        }

        //Player is challenger, mark that it has read the challenge
        if($challenge->getChallengedPlayer()->getId() == $player->getId() && $challenge->getState() == 0){
            $challenge->setState(1);
            $result = $this->processChallenge($challenge);
            if($result != null)
                return $result;
        }
        //Player is challenged one, mark that it has read the outcome
        else if($challenge->getChallengerPlayer()->getId() == $player->getId() && ($challenge->getState() == 2 || $challenge->getState() == 3)){
            $challenge->setState(4);
            $result = $this->processChallenge($challenge);
            if($result != null)
                return $result;
        }
        else if ($challenge->getChallengerPlayer()->getId() != $player->getId() && $challenge->getChallengedPlayer()->getId() != $player->getId()){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Supplied token does not belong to challenger or challenged player'), 400);
        }

        return array('success' => 'true', 'data' => array($challenge->toJson()));

    }

    protected function GetPlayerByFbId($fbId){
        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:Player');

        return $repository->findOneByFbId($fbId);
    }

    protected function GetPlayerById($id){
        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:Player');

        return $repository->findOneById($id);
    }


    /**
     * @param Player $challenger
     * @param Player $challenged
     */
    public function GetActiveChallenges($player){

        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:Challenge');


        $qb = $repository->createQueryBuilder('c');
        $qb->where('c.challengerPlayer = :challenger')
            ->setParameter('challenger', $player)
            ->orWhere('c.challengedPlayer = :challenged')
            ->setParameter('challenged', $player)
            ->andWhere('c.state != 4');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Player $challenger
     * @param Player $challenged
     */
    public function GetActiveChallenge($challenger, $challenged){

        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:Challenge');


        $qb = $repository->createQueryBuilder('c');
        $qb->where('c.challengerPlayer = :challenger')
            ->setParameter('challenger', $challenger)
            ->andWhere('c.challengedPlayer = :challenged')
            ->setParameter('challenged', $challenged)
            ->andWhere('c.state < 2');

        return $qb->getQuery()->getResult();
    }

    public static function GetRewardFromTypeAndValue($type, $value){
        if($type == 0)
            return $value * 1.5;
        else if ($type == 1)
            return 50 + sqrt($value)* 2;
        else
            return 50;
    }

    /**
     * @View(statusCode="200")
     */
    public function postNewRandomAction(Request $request){
        /**
         * @var Player
         */
        $player = $this->GetPlayerByToken($request);

        if($player == null){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Token invalid'), 400);
        }

        $stmt = $this->getDoctrine()->getEntityManager()
            ->getConnection()
            ->prepare('SELECT * FROM player WHERE id NOT IN
                      (SELECT a.id FROM ((select c1.challenger_id as id from challenge c1 where c1.challenged_id = :id AND c1.state < 2) UNION
                      (select c.challenged_id as id from challenge c where c.challenger_id = :id AND c.state < 2)) AS a)
                      AND ID != :id ORDER BY RAND() LIMIT 1');
        $stmt->bindValue("id", $player->getId());
        $stmt->execute();
        $result = $stmt->fetchAll();



        if(count($result) == 1){
            $opponent = $result[0];

            $challenge = new Challenge();
            $challenge->setChallengedPlayer($this->GetPlayerById($opponent['id']));
            $challenge->setChallengerPlayer($this->GetPlayerById($player->GetId()));
            $challenge->setState(0);
            $type = $request->request->get("type");
            $value = $request->request->get("value");
            $challenge->setType($type);
            $challenge->setValue($value);
            $challenge->setReward(ChallengeController::GetRewardFromTypeAndValue($type, $value));
            /**
             * @var \FOS\RestBundle\View\View
             */
            $view = $this->processChallenge($challenge);

            //Errors have happened when trying to validate challenge
            if($view != null)
                return $view;

            //Looks like the new challenge was created successfully
            return array('success' => 'true', 'data' => array(array(
                'id' => $opponent['id'],
                'name' => $opponent['name'],
                'firstName' => $opponent['first_name'],
                'lastName' => $opponent['surname'],
                'fb_id' => $opponent['fb_id'],
                'distance' => $opponent['id']
            )));
        }

        return array('success' => 'false', 'error' => 5, 'errorMsg' => 'We could not find any challenges for you at this moment');
    }

    /**
     * @View(statusCode="204")
     */
    public function postNewAction(Request $request)
    {
        $player = $this->GetPlayerByToken($request);

        if($player == null){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Token invalid'), 400);
        }

        //print_r(implode(',', $request->request->all()) . "XXXX");

        $challenger = $this->GetPlayerByFbId($request->request->get("challenger_fbid"));
        $challenged = $this->GetPlayerByFbId($request->request->get("challenged_fbid"));

        if($challenger == false)
            return $this->view(array('success' => 'false', 'errorMsg' => 'Challenger facebook id is invalid'), 400);

        if($challenged == false)
            return $this->view(array('success' => 'false', 'errorMsg' => 'Challenged facebook id is invalid'), 400);

        //Player is not challenger
        if($challenger->getId() != $player->getId()){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Only challenger can create a challenge'), 400);
        }

        $activeChallenges = $this->GetActiveChallenge($challenger, $challenged);

        if(count($activeChallenges) > 0)
            return $this->view(array('success' => 'false', 'errorMsg' => 'Can not create new challenge, challenge already active.'), 400);

        $challenge = new Challenge();
        $challenge->setChallengedPlayer($challenged);
        $challenge->setChallengerPlayer($challenger);
        $challenge->setState(0);
        $type = $request->request->get("type");
        $value = $request->request->get("value");
        $challenge->setType($type);
        $challenge->setValue($value);
        $challenge->setReward(ChallengeController::GetRewardFromTypeAndValue($type, $value));

        return $this->processChallenge($challenge);
    }


    /**
     * @View(statusCode="204")
     */
    public function postAction(Challenge $challenge, Request $request)
    {

        $player = $this->GetPlayerByToken($request);

        if($player == null){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Token invalid'), 400);
        }

        if($challenge->getState() == 4){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Can not update challenge that is already finished'), 400);
        }

        //Player is challenger. 
        if($challenge->getChallengerPlayer()->getId() == $player->getId()){
	    //If state is not 4 or 1 we should not be able to update the state
	    if($request->request->get("state") != 4 && $request->request->get("state") != 1){
            	return $this->view(array('success' => 'false', 'errorMsg' => 'Challenger can not update a challenge to other but state 4 or 1'), 400);
	    }
        }
        //Player is not challenged one
        else if($challenge->getChallengedPlayer()->getId() != $player->getId()){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Token does not belong to challenged player'), 400);
        }

        $challenge->setState($request->request->get("state"));

        //state == 2 challenger won, state == 3 challenged won
        if($challenge->getState() == 2)
            $challenge->setWinnerPlayer(($challenge->getChallengerPlayer()));
        else if($challenge->getState() == 3)
            $challenge->setWinnerPlayer(($challenge->getChallengedPlayer()));


        return $this->processChallenge($challenge);
    }


    /**
     * @View(statusCode="200")
     */
    public function getChallengerAction($challenger_id, $challenged_id)
    {
        $this->container->get('logger')->info('Local variables', get_defined_vars());

        $token = $this->getRequest()->query->get('token');
        if(true !== ($rsp = $this->handleIsAuthorised($player, $token))){
            return $rsp;
        }

        return array('success' => 'true', 'data' => array($player->toJson(true, false)));
    }

    /**
     * Update item
     *
     * @param Challenge $challenge
     */
    protected function processChallenge( Challenge $challenge) {
        $new = (NULL === $challenge->getId())?true:false;

        $validator = $this->get('validator');
        $errors = $validator->validate($challenge);

        if (count($errors) > 0) {
            return $this->view($errors, 400);
        } else {

            $em = $this->getDoctrine()->getManager();
            $em->persist($challenge);

            $this->sendNotificationIfNecessary($challenge);
            try {
                $em->flush();
            } catch (DBALException  $e) {
                //print_r($e->getMessage());die('foo');
                return $this->view($e->getMessage(), 400);
            }
        }
    }

    protected function sendNotificationIfNecessary(Challenge $challenge){
        //It is a new challenge
        if($challenge->getState() == 0){
            //If token present
            if(strlen($challenge->getChallengedPlayer()->getApplePushToken()) > 5){
                //limit is 100 characters
                $message = new iOSMessage();
                $maxNameLength = 30;
                $message->setMessage(substr($challenge->getChallengerPlayer()->getName(), 0, $maxNameLength) . (strlen($challenge->getChallengerPlayer()->getName()) > $maxNameLength ? '...' : '') .
                ' has challenged you to ' . ($challenge->getType() == 0 ?
                    ("collect more than " . $challenge->getValue() . " coins!") :
                    ("run further than " . $challenge->getValue() . " meters!")));
                $message->setAPSSound("default");
                $message->setDeviceIdentifier(str_replace('%', '', $challenge->getChallengedPlayer()->getApplePushToken()));
                $this->container->get('rms_push_notifications')->send($message);

                $this->container->get('logger')->info('Notifying player id: ' . $challenge->getChallengedPlayer()->getId() . " that his friend id: " . $challenge->getChallengerPlayer()->getId() . " has started a new challenge id : " . $challenge->getId() , get_defined_vars());
            }

        }
        //Challenge has been won or lost
        else if($challenge->getState() == 2 || $challenge->getState() == 3){
            //If token present
            if(strlen($challenge->getChallengerPlayer()->getApplePushToken()) > 5){
                //limit is 100 characters
                $message = new iOSMessage();
                $maxNameLength = 30;
                $challengedPlayerName = substr($challenge->getChallengedPlayer()->getName(), 0, $maxNameLength) . (strlen($challenge->getChallengedPlayer()->getName()) > $maxNameLength ? '...' : '');

                $message->setMessage($challenge->getState() == 2 ?
                'You have WON a challenge against ' . $challengedPlayerName . ". Claim your reward!" :
                'You have LOST a challenge against ' . $challengedPlayerName . ".");
                $message->setAPSSound("default");
                $message->setDeviceIdentifier(str_replace('%', '', $challenge->getChallengerPlayer()->getApplePushToken()));
                $this->container->get('rms_push_notifications')->send($message);

                $this->container->get('logger')->info('Notifying player id: ' . $challenge->getChallengerPlayer()->getId() . " that his friend id: " . $challenge->getChallengerPlayer()->getId() . " has " . ($challenge->getState() == 2 ? "lost" : "won") . "  a challenge id : " . $challenge->getId(), get_defined_vars());
            }
        }

    }

}
