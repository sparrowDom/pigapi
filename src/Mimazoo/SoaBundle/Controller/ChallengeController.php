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

    /**
     * @View(statusCode="204")
     */
    public function postAction(Request $request)
    {
        $player = $this->GetPlayerByToken($request);

        if($player == null){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Token invalid'), 400);
        }

        print_r(implode(',', $request->request->all()) . "XXXX");

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

    protected function GetPlayerByFbId($fbId){
        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:Player');

        return $repository->findOneByFbId($fbId);
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
            ->andWhere('c.state != 4');

        return $qb->getQuery()->getResult();
    }

    public static function GetRewardFromTypeAndValue($type, $value){
        if($type == 0)
            return $value * 1.5;
        else if ($type == 1)
            return $value / 8;
        else
            return 50;
    }

    /**
     * @View(statusCode="204")
     */
    public function putAction(Challenge $challenge, Request $request)
    {

        $player = $this->GetPlayerByToken($request);

        if($player == null){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Token invalid'), 400);
        }

        if($challenge->getState() == 4){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Can not update challenge that is already finished'), 400);
        }

        //Player is challenger
        if($challenge->getChallengerPlayer()->getId() == $player->getId()){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Challenger can not update a challenge'), 400);
        }
        //Player is not challenged one
        else if($challenge->getChallengedPlayer()->getId() != $player->getId()){
            return $this->view(array('success' => 'false', 'errorMsg' => 'Token does not belong to challenged player'), 400);
        }

        $challenge->setState($request->request->get("state"));

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

        return array('success' => 'true', 'data' => array($player->toJson()));
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
            try {
                $em->flush();
            } catch (DBALException  $e) {
                //print_r($e->getMessage());die('foo');
                return $this->view($e->getMessage(), 400);
            }
        }
    }

}
