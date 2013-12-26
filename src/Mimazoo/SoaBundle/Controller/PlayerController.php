<?php

namespace Mimazoo\SoaBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
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
use Mimazoo\SoaBundle\Hal\Hal;

use Mimazoo\SoaBundle\Form\Type\PlayerType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Util\Debug;
use Symfony\Component\Translation\Tests\String;
use RMS\PushNotificationsBundle\Message\iOSMessage;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Player controller
 */
class PlayerController extends Controller
{
	
	protected $patchWhitelist = array(
		'slug',
	);
	
    /**
	 * @View(statusCode="200")
	 *
	 * @return Hal
	 */
    /*
    public function cgetAction(ParamFetcher $paramFetcher)
    {

    }
    */

    /**
     * @View(statusCode="200")
     */
    public function meAction(Request $request){

        $player = $this->GetPlayerByToken($request);

        if($player == null){
            return array('success' => 'false', 'error' => 10, 'errorMsg' => 'Token invalid');
        }

        $response = $this->forward('MimazooSoaBundle:Player:get', array(
            'id'  => $player->getId(),
            'player' => $player
        ),
        array('token' => $player->getFbAccessToken()));

        return $response;

    }

    /**
     * @View(statusCode="200")
     */
    public function alltimeHighscoresAction(Request $request){
        $player = $this->GetPlayerByToken($request);

        if($player == null){
            return array('success' => 'false', 'error' => 10, 'errorMsg' => 'Token invalid');
        }

        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:Player');


        $qb = $repository->createQueryBuilder('p');
        $qb->orderBy('p.distanceBest', 'DESC')
            ->setMaxResults(20);


        $players = array();
        $count = 0;
        $playerInResultSet = false;
        foreach($qb->getQuery()->getResult() as $topPlayer){
            $count++;
            if($topPlayer->getId() == $player->getId()){
                $playerInResultSet = true;
            }
            $players[] = $topPlayer->toJson(true, false, $count);
        }

        //Player not in top players list. Find and add him to the results
        if(!$playerInResultSet){
            //Remove last inserted player
            array_pop($players);
            $q = $this->getDoctrine()->GetConnection($this->getDoctrine()->getDefaultConnectionName());
            /*
             * $var Doctrine\DBAL\Driver\PDOStatement
             */
            $result = $q->query("select *, FIND_IN_SET(distance_best, ( SELECT GROUP_CONCAT( distance_best ORDER BY distance_best DESC) FROM player)) as rank FROM player where id=" . $player->GetId() . ";");
            foreach($result as $value){
                $players[] = array('id' => intval($value['id']),
                                   'name' => $value['name'],
                                   'firstName' => $value['first_name'],
                                   'lastName' => $value['surname'],
                                   'fb_id' => $value['fb_id'],
                                   'present_id' => intval($value['present_selected']),
                                   'distance' => intval($value['distance_best']),
                                   'rank' => intval($value['rank']),
                );

                //should never be more than 1 result
                break;
            }
        }

        return array('success' => 'true', 'data' => $players);

    }

    /**
     * @View(statusCode="200")
     */
    public function loginAction(Request $request){

        $token = $request->query->get('token');
        /* @var $facebook \FOS\FacebookBundle\FOSFacebookBundle */
        $facebook = $this->get('facebook');
        //If token is empty string it does not get set
        $facebook->SetAccessToken(strlen($token) > 0 ? $token : 'empty');

        $isNewPlayer = false;

        try{
            $fbResult = $facebook->api('/me', 'GET', array("fields" => "id"));

            if($facebook->SetExtendedAccessToken() !== false){

                $currentPlayer = $this->UpdateTokenInformation($facebook, $fbResult['id']);
                //If false means we need to handle a new player
                if($currentPlayer == false){
                    $fbResult = $facebook->api('/me', 'GET', array('fields' => 'id,name,friends,email,first_name,last_name'));

                    $player = new Player();
                    $player->setFirstName($fbResult['first_name']);
                    $player->setSurname($fbResult['last_name']);
                    $player->setName($fbResult['name']);
                    $player->setFbId($fbResult['id']);
                    $player->setFbAccessToken($facebook->getAccessToken());
                    $player->setSlug(mb_strtolower(str_replace(' ', '_', $fbResult['name'])));
                    $player->setDistanceBest(-1);
                    $player->setPresentSelected(0);

                    $friends = $this->getFriends($fbResult['friends']);
                    $player->setFriends($friends);

                    $errorView = $this->processPlayer($player);
                    $currentPlayer = $player;

                    if($errorView != null)
                        return $errorView;

                    $this->addANewFriend($player, $friends);

                    $isNewPlayer = true;
                }
            }
            else{
                return array('success' => 'false', 'error' => 12, 'errorMsg' => 'Can not extend token lifetime');
            }
        }
        catch(FacebookApiException $e){
            return $this->handleFacebookApiError($e, $facebook, $token);
        }

        $this->getLogger()->info("User logged in id:" . $currentPlayer->getId());

        $json = array('success' => 'true', 'access_token' => $facebook->GetAccessToken());

        if(is_object($currentPlayer))
            $json['id'] = $currentPlayer->getId();

        if($isNewPlayer)
            $json['msg'] = 'New User';

        return $json;
    }

    protected function addANewFriend($newPlayer, $facebookFriends){
        $em = $this->getDoctrine()->getManager();
        foreach($facebookFriends as $friend){
            $friend->addFriend($newPlayer);
            $em->persist($friend);

            /*
            $pid = pcntl_fork();
            $this->getLogger()->info("FU");
            if ($pid == -1) {
                $this->getLogger()->err("could not fork");
            } else if ($pid) {
                // we are the parent
                $this->getLogger()->info("waiting for children to finish: " . pcntl_waitpid($pid, $status, WNOHANG OR WUNTRACED));
                while(pcntl_waitpid($pid, $status, WNOHANG OR WUNTRACED) > 0) {
                    $this->getLogger()->info("still not finished");
                    usleep(500);
                }
                $this->getLogger()->info("children finished notifying friends child pid: " . $pid);
                posix_kill($pid, 0);
                $this->notifyNewFriendPlaying($friend, $newPlayer);
                $this->getLogger()->info("exiting");
                die();

            } else {
                $this->getLogger()->info("CHILD BEFORE PID:" . getmypid());
                sleep(5);
                $this->getLogger()->info("CHILD AFTER");
                // we are the child
            }
            */
            $this->notifyNewFriendPlaying($friend, $newPlayer);

        }
        $em->flush();
        return true;
    }

    protected function notifyNewFriendPlaying($oldPlayer, $newPlayer){
        if(strlen($oldPlayer->getApplePushToken()) > 5){
            //limit is 100 characters
            $message = new iOSMessage();
            $maxNameLength = 30;
            $message->setMessage("Your friend " . substr($newPlayer->getName(), 0, $maxNameLength) . (strlen($newPlayer->getName()) > $maxNameLength ? '...' : '') .
            ' has started playing the game. Challenge him!');

            $message->setAPSSound("default");
            $message->setDeviceIdentifier(str_replace('%', '', $oldPlayer->getApplePushToken()));
            $this->container->get('rms_push_notifications')->send($message);

            $this->container->get('logger')->info('Notifying player id: ' . $oldPlayer->getId() . " that his friend id: " . $newPlayer->getId() . " has started playing the game" , get_defined_vars());
        }
    }

    protected function getFriends($facebookFriends){
        $fbIds = array();
        if(isset($facebookFriends['data'])){
            foreach($facebookFriends['data'] as $friend){
                $fbIds[] = $friend['id'];
            }

            $repository = $this->getDoctrine()
                ->getRepository('MimazooSoaBundle:Player');
            $playerFriends = $repository->findByFbId($fbIds);

            $retVal = new ArrayCollection();
            foreach($playerFriends as $friend)
                $retVal->add($friend);

            return $retVal;
        }

        return false;
    }



    /**
     * @View(statusCode="200")
     */
    public function getAction($id, Player $player)
    {
        $token = $this->getRequest()->query->get('token');
        if(true !== ($rsp = $this->handleIsAuthorised($player, $token))){
            return $rsp;
        }


        return array('success' => 'true', 'data' => array($player->toJson()));
    }

    /*
     * I guess we could expand this sometime in the future
     */
    public function handleIsAuthorised(Player $player, $token){
        if(strcmp($player->getFbAccessToken(), $token) != 0){
            //Authorisation was ok but the resource is forbidden
            return $this->view(array('success' => 'false', 'error' => 14, 'errorMsg' => 'Forbidden resource'), 403);
        }
        return true;
    }

    /**
     * @View(statusCode="201")
     */
    /*
    public function postAction()
    {
        $request = $this->getRequest();
        print_r($request->request);exit;
    	return $this->processPlayer(new Player());
    }
    */

    /**
     * @View(statusCode="204")
     */
    public function postAction(Player $player)
    {

        $request = $this->getRequest();
        $token = $request->query->get('token');
        if(true !== ($rsp = $this->handleIsAuthorised($player, $token))){
            return $rsp;
        }

        $distance = $request->request->get("distance");
        if($distance != false){
            $distance = intval($distance);
            if($player->getDistanceBest() < $distance){
                $previousDistanceBest = $player->getDistanceBest();
                $this->checkIfBeatenHighscoreOfFriends($player, $previousDistanceBest, $distance);
                $player->setDistanceBest($distance);
                $this->getLogger()->info("Updating player distance id: " . $player->getId());
                //return $this->processPlayer($player);
            }
        }

        $pushToken = $request->request->get("apple_push_token");
        if($pushToken != false){
            $player->setApplePushToken(trim($pushToken));
            $this->getLogger()->info("Updating player push token id: " . $player->getId());
        }

        $present_id = $request->request->get("present_id");
        if($present_id != false){
            $present_id = intval($present_id);
            $player->setPresentSelected($present_id);
            $this->getLogger()->info("Updating player present_id id: " . $player->getId());
            //return $this->processPlayer($player);
        }

    	return $this->processPlayer($player);
    }

    /**
     * @View(statusCode="204")
     */
    public function updateAction(Request $request){

        $token = $request->query->get('token');
        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:Player');

        /* @var $player \Mimazoo\SoaBundle\Entity\Player */
        $player = $repository->findOneByFbAccessToken($token);

        if($player == null){
            return $this->view(array('success' => 'false', 'error' => 10, 'errorMsg' => 'Token invalid'), 400);
        }

        $distance = $request->request->get("distance");
        if($distance != false){
            $distance = intval($distance);
            if($player->getDistanceBest() < $distance){
                $previousDistanceBest = $player->getDistanceBest();
                $this->checkIfBeatenHighscoreOfFriends($player, $previousDistanceBest, $distance);
                $player->setDistanceBest($distance);
                //return $this->processPlayer($player);
            }
        }

        $present_id = $request->request->get("present_id");
        if($present_id != false){
            $present_id = intval($present_id);
            $player->setPresentSelected($present_id);
            //return $this->processPlayer($player);
        }

        $this->getLogger()->info("Updating player id: " . $player->getId());
        return $this->processPlayer($player);
    }

    private function checkIfBeatenHighscoreOfFriends(Player $player, $previousDistanceBest, $newDistanceBest){
        foreach($player->getFriends() as $friend){
            if($friend->getDistanceBest() >= $previousDistanceBest &&
               $friend->getDistanceBest() < $newDistanceBest &&
                strlen($friend->getApplePushToken()) > 0){

                $message = new iOSMessage();
                $maxNameLength = 30;
                $message->setMessage("Your friend " . substr($player->getName(), 0, $maxNameLength) . (strlen($player->getName()) > $maxNameLength ? '...' : '') .
                ' has beaten your high-score!');

                $message->setAPSSound("default");
                $message->setDeviceIdentifier(str_replace('%', '', $friend->getApplePushToken()));
                $this->container->get('rms_push_notifications')->send($message);
            }
        }
    }

    /**
     * @View(statusCode="204")
     */
    /*
    public function patchAction(Player $player)
    {
        print_r($this->getRequest()->request);die;
    	return $this->patch($player);
    }
    */
    /**
     * @View(statusCode="204")
     */
    /*
    public function deleteAction(Player $player)
    {
    	$em = $this->getDoctrine()->getManager();
    	$em->remove($player);
    	$em->flush();
    }
    */

    protected function UpdateTokenInformation(FacebookSessionPersistence $facebook, $fbid){
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:Player');

        $player = $repository->findOneByFbId($fbid);

        if($player != false){
            $player->setFbAccessToken($facebook->getAccessToken());
            $em->persist($player);
            $em->flush();

            $this->getLogger()->info("Updating token information for player id: " . $player->getId());
            return $player;
        }

        //Player is new
        return false;
    }

    /**
     * Update item
     *
     * @param Player $player
     */
    protected function processPlayer( Player $player) {
        $new = (NULL === $player->getId())?true:false;

        $validator = $this->get('validator');
        $errors = $validator->validate($player);

        if (count($errors) > 0) {
            return $this->view($errors, 400);
        } else {


            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            try {
                $em->flush();
            } catch (DBALException  $e) {
                //print_r($e->getMessage());die('foo');
                return $this->view($e->getMessage(), 400);
            }
        }
    }

}
