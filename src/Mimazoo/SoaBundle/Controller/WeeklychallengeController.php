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

        
    }
    /**
     * @View(statusCode="200")
     *
     */

    public function getCurrentAction(Request $request)
    {
        $player = $this->GetPlayerByToken($request);
        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:WeeklyChallenge');

        $qb = $repository->createQueryBuilder('wc');
        $qb->where('wc.isCompleted=false')
           ->orderBy('wc.id', 'ASC')
           ->setMaxResults(1);

        $result = $qb->getQuery()->getResult();

        if(count($result) == 1)
            return array('success' => 'true', 'data' => array($result[0]->toJson()));
        else
            return array('success' => 'true', 'data' => array());
    }

    /**
     * @View(statusCode="204")
     */
    public function patchAction(Request $request){
        if(true !== ($view = $this->validatePlayerIsSuperUser($request)))
            return $view;

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

    protected function validatePlayerIsSuperUser($request){
        $player = $this->GetPlayerByToken($request);

        if($player == null)
            return array('success' => 'false', 'error' => 10, 'errorMsg' => 'Token invalid');
        else if($player->getIsSuperUser() !== true)
            return array('success' => 'false', 'error' => 19, 'errorMsg' => 'User not authorized to perform action');

        return true;
    }

    /**
     * Update item
     *
     * @param WeeklyChallenge
     */
    protected function process( WeeklyChallenge $challenge) {
        $new = (NULL === $challenge->getId())?true:false;

        $validator = $this->get('validator');
        $errors = $validator->validate($challenge);

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
