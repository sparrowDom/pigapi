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


use Mimazoo\SoaBundle\Entity\Notification;
use Mimazoo\SoaBundle\Hal\Hal;

use Mimazoo\SoaBundle\Form\Type\PlayerType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Util\Debug;
use Symfony\Component\Translation\Tests\String;
use RMS\PushNotificationsBundle\Message\iOSMessage;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Notification controller
 */
class NotificationController extends Controller
{

    /**
     * @View(statusCode="204")
     */
   /* public function postAction(Request $request)
    {

        $request = $this->getRequest();

        $token = $request->request->get("apple_push_token");

        if($token == null){
            return $this->view(array('success' => 'false', 'error' => 10, 'errorMsg' => 'apple_push_token is a required POST field'), 400);
        }

        $notification = new Notification();
        $notification->setApplePushToken($token);

        return $this->processNotification($notification);
    }*/

    /**
     * Update item
     */
    /*protected function processNotification( Notification $notification) {
        $validator = $this->get('validator');
        $errors = $validator->validate($notification);

        if (count($errors) > 0) {
            return $this->view($errors, 400);
        } else {


            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            try {
                $em->flush();
            } catch (DBALException  $e) {
                //print_r($e->getMessage());die('foo');
                return $this->view($e->getMessage(), 400);
            }
        }
    }*/
}