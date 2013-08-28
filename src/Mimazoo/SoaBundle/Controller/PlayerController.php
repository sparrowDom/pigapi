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

        $token = $request->query->get('token');
        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:Player');

        $player = $repository->findOneByFbAccessToken($token);

        if($player == null){
            return array('success' => 'false', 'error' => 10, 'errorMsg' => 'Token invalid');
        }


        $response = $this->forward('MimazooSoaBundle:Player:get', array(
            'id'  => $player->getId(),
            'player' => $player
        ),
        array('token' => $token));

        return $response;

    }

    /**
     * @View(statusCode="200")
     */
    public function loginAction(Request $request){

        $token = $request->query->get('token');
        /* @var $facebook \FOS\FacebookBundle\FOSFacebookBundle */
        $facebook = $this->get('facebook');
        $facebook->SetAccessToken($token);

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
            $this->notifyNewFriendPlaying($friend, $newPlayer);
        }
        $em->flush();
        return true;
    }

    protected function notifyNewFriendPlaying($oldPlayer, $newPlayer){

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
        $this->container->get('logger')->info('Local variables', get_defined_vars());

        $token = $this->getRequest()->query->get('token');
        if(true !== ($rsp = $this->handleIsAuthorised($player, $token))){
            return $rsp;
        }

        return array('success' => 'true', 'data' => array($player->toJson()));
    }

    /*
     * I guess we could expand this sometime in the future
     */
    protected function handleIsAuthorised(Player $player, $token){
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
    public function putAction(Player $player)
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
                $player->setDistanceBest($distance);
                return $this->processPlayer($player);
            }
        }

        $present_id = $request->request->get("present_id");
        if($present_id != false){
            $present_id = intval($present_id);
            $player->setPresentSelected($present_id);
            return $this->processPlayer($player);
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
