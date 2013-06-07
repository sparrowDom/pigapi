<?php

namespace Mimazoo\SoaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

use Mimazoo\SoaBundle\Controller\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;

use Mimazoo\SoaBundle\Entity\Player;
use Mimazoo\SoaBundle\Hal\Hal;

use Mimazoo\SoaBundle\Form\Type\PlayerType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Util\Debug;

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
    public function cgetAction(ParamFetcher $paramFetcher)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$alias = 'p';
    	$dql = "SELECT {$alias} FROM MimazooSoaBundle:Player {$alias}";
    	$query = $em->createQuery($dql);
    		
    	return $this->paginate($paramFetcher, $query, $alias);
    }
    
    
    /**
     * @View(statusCode="200")
     */
    public function getAction($id, Player $player)
    {
    	return new Hal($this->getRequest()->getUri(), $player);
    }
    
    /**
     * @View(statusCode="201")
     */
    public function cpostAction()
    {
    	return $this->processForm(new Player());
    }
    
    /**
     * @View(statusCode="204")
     */
    public function putAction(Player $player)
    {
    	return $this->processForm($player);
    }
    
    /**
     * @View(statusCode="204")
     */
    public function patchAction(Player $player)
    {
    	return $this->patch($player);
    }
    
    /**
     * @View(statusCode="204")
     */
    public function deleteAction(Player $player)
    {
    	$em = $this->getDoctrine()->getManager();
    	$em->remove($player);
    	$em->flush();
    }
    
}
