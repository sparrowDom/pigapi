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


use Mimazoo\SoaBundle\Entity\UserDatas;
use Mimazoo\SoaBundle\Hal\Hal;

use Mimazoo\SoaBundle\Form\Type\PlayerType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Util\Debug;
use Symfony\Component\Translation\Tests\String;
use RMS\PushNotificationsBundle\Message\iOSMessage;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * User statistics controller
 */
class UserStatisticsController extends Controller
{

    /**
     * @View(statusCode="204")
     */
    public function postAction(Request $request)
    {

        $request = $this->getRequest();

        $statistics = new UserDatas();

        $distance = $request->request->get("distance");
        if($distance != false){
            $distance = intval($distance);
            $statistics->setDistanceBest($distance);
        }

        $storyProgress = $request->request->get("storyProgress");
        if($storyProgress != false){
            $storyProgress = intval($storyProgress);
            $statistics->setStoryProgress($storyProgress);
        }

        $coins = $request->request->get("coins");
        if($coins != false){
            $coins = intval($coins);
            $statistics->setCoins($coins);
        }

        $gamesPlayed = $request->request->get("gamesPlayed");
        if($gamesPlayed != false){
            $gamesPlayed = intval($gamesPlayed);
            $statistics->setGamesPlayed($gamesPlayed);
        }

        $userId = $request->request->get("userId");
        if($userId != false){
            $userId = intval($userId);
            $statistics->setUserId($userId);
        }

        $pushToken = $request->request->get("token");
        if($pushToken != false){
            $statistics->setApplePushToken(trim($pushToken));
        }

        $this->getLogger()->info("Adding statistics entry userId: " . $userId  . "to database!");

        return $this->processStatistics($statistics);
    }

    /**
     * Update item
     *
     * @param UserDatas $userdatas
     */
    protected function processStatistics(UserDatas $userdatas) {
        $validator = $this->get('validator');
        $errors = $validator->validate($userdatas);

        if (count($errors) > 0) {
            return $this->view($errors, 400);
        } else {


            $em = $this->getDoctrine()->getManager();
            $em->persist($userdatas);
            try {
                $em->flush();
            } catch (DBALException  $e) {
                //print_r($e->getMessage());die('foo');
                return $this->view($e->getMessage(), 400);
            }
        }
    }
}