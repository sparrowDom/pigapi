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
use Mimazoo\SoaBundle\Entity\Challenge;
use Mimazoo\SoaBundle\Hal\Hal;

use Mimazoo\SoaBundle\Form\Type\PlayerType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Util\Debug;
use Symfony\Component\Translation\Tests\String;

/**
 * Challenge controller
 */
class ChallengeController extends Controller
{

    /**
     * @View(statusCode="200")
     *
     */

    public function cgetAction(Request $request)
    {
        print_r("DREK");
    }

    /**
     * @View(statusCode="200")
     */
    public function getAction($id, Challenge $challenge)
    {
        $this->container->get('logger')->info('Local variables', get_defined_vars());

        $token = $this->getRequest()->query->get('token');
        if(true !== ($rsp = $this->handleIsAuthorised($player, $token))){
            return $rsp;
        }

        return array('success' => 'true', 'data' => array($player->toJson()));
    }

}
