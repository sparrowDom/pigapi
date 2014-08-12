<?php

namespace Mimazoo\SoaBundle\Controller;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\FacebookBundle\Facebook\FacebookSessionPersistence;

use Mimazoo\SoaBundle\Hal\Hal;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Doctrine\ORM\Query;

use Symfony\Component\HttpFoundation\JsonResponse;
use Mimazoo\SoaBundle\Entity\Entity;
use Symfony\Component\HttpFoundation\ParameterBag;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Doctrine\DBAL\DBALException;
use Doctrine\Common\Util\Debug;


class Controller extends FOSRestController implements ClassResourceInterface
{
	
	/**
	 * Resource name in lowercase, if not set, it is taken from class name
	 * 
	 * @var string
	 */
	protected $resourceName;
	
	
	/**
	 * Array of fields we can patch update
	 * 
	 * @var array
	 */
	protected $patchWhitelist;
	
	
	/**
	 * Paging params set 
	 *
	 * @var array
	 */
	protected $pagingParams;

    /**
     * Default logger
     *
     * @var Logger
     */
    protected $logger;
	
	
	/**
	 * Current page wildcard params used for generating urls
	 * 
	 * @var array
	 */
	protected $wildcardParams = array();

	const EXPAND_PARAM_NAME = 'expand';

    /**
     * * @return Logger
     */
    protected function getLogger(){
        if($this->logger == null)
            $this->logger = $this->get('logger');

        return $this->logger;
    }

	/**
	 * Paginate query results using knp paginator bundle
	 * 
	 * @param ParamFetcher $paramFetcher
	 * @param Query $query
	 * @param string $sortAlias
	 * @return Hal
	 */
	protected function paginate(ParamFetcher $paramFetcher, $query, $sortAlias)
	{
		$request = $this->getRequest();
		
		$params = $paramFetcher->all() + $this->getPagingParams();

		//alternative page start index support
		if (!empty($params['pageStartIndex'])) {
			$page = abs(round($params['pageStartIndex']/$params['pageSize'])) + 1;
		}
		
		$aliasPrefix = $sortAlias . '.';
		
		//paginator
		$paginator = $this->get('knp_paginator');
		
		//sort fields resource values to entity fields conversion
		if (!empty($params['sortBy']) && substr($params['sortBy'], 0, 2) != $aliasPrefix) {
			$_GET['sortBy'] =  $aliasPrefix . $params['sortBy'];
		//set default sortBy if none is set	
		} else {
			//$_GET['sortBy'] = $aliasPrefix . 'id';
		}
		
		if (empty($params['sortOrder'])) {
			//$_GET['sortOrder'] = 'asc';
		}
	
		$items = $paginator->paginate($query, $params['page'], $params['pageSize']);
		
		$paginationData = $items->getPaginationData();
		
		//root data
		$rootArr = array(
				'totalCount' => $paginationData['totalCount'],
				'pageCount' => $paginationData['pageCount'],
				'pageSize' => $paginationData['numItemsPerPage'],
				'currentPage' => intval($paginationData['current']),
				'currentPageItemCount' => $paginationData['currentItemCount'],
		);
		
		$entityName = $this->getEntityNameFromResourceObject($items);
		
		$hal = new Hal($request->getUri(), $rootArr, $entityName);
		
		//paging links
		$this->addPagingLinks($hal, $paginationData);
		

		//collection output
		foreach ($items as $item) {
		
			$hal->addResource(
					$this->getResourceName(),
					new Hal(
							$this->getResourceUrl($item, $params),
							$item
					)
			);
		}
		
		return $hal;
		
	}

    protected function GetPlayerByToken(Request $request){
        $token = $request->query->get('token');
        $deviceToken = $request->query->get('deviceToken');

        $repository = $this->getDoctrine()
            ->getRepository('MimazooSoaBundle:Player');

        $player = $repository->findOneByFbAccessToken($token);
        if($player == null)
            $player = $repository->findOneByDeviceAccessToken($deviceToken);

        return $player;
    }

    protected function handleFacebookApiError(\FacebookApiException $exception, FacebookSessionPersistence $facebook, $token){
        $result = $exception->getResult();
        if(isset($result['error'])){
            $error = $result['error'];
            if(isset($error['code'])){
                $code = $error['code'];
                if(isset($error['error_subcode'])){
                    $subCode = $error['error_subcode'];

                    if($code == 190){
                        if($subCode == 463){
                            return array('success' => 'false', 'error' => 11, 'errorMsg' => 'Token expired');
                        }
                        else{
                            return array('success' => 'false', 'error' => 10, 'errorMsg' => 'Token invalid');
                        }
                    }
                    else{
                        return array('success' => 'false', 'error' => 12, 'errorMsg' => "Token invalid code: $code Error: " . implode($error, ','));
                    }
                }
                else{
                    return array('success' => 'false', 'error' => 67, 'errorMsg' => "Unknown error, please try again later. Error: " . implode($error, ','));
                }
            }
            else{
                return array('success' => 'false', 'error' => 68, 'errorMsg' => "Unknown error, please try again later. Error: " . implode($error, ','));
            }
        }
        else{
            return array('success' => 'false', 'error' => 69, 'errorMsg' => 'Unknown error, please try again later');
        }
    }

	/**
	 * Default resource link generation
	 * 
	 * @param object $entityObj
	 * @return string
	 */
	protected function getResourceUrl($entityObj) {
		return $this->generateUrl(
				'get_' . $this->getEntityNameFromResourceObject($entityObj),
				array('id' => $entityObj->getId()),
				true
		);
	}
	
	/**
	 * Get entity name for entity object or pagination iterator of entity objects
	 * 
	 * @param object|array $resource
	 */
	protected function getEntityNameFromResourceObject($resource) {
		
		$exceptionText = 'Input should be a entity object or array of such objects.';
		if (is_object($resource) && $resource instanceof SlidingPagination) {
			foreach ($resource as $item) {
				if ($item instanceof Entity) {
					return $item->getLowerCaseEntityName();
				} else {
					throw new \Exception($exceptionText);
				}
			} 
			
			//empty page, no resources
			return null;
			
		
		} else if (is_object($resource) && $resource instanceof Entity) {
			return $resource->getLowerCaseEntityName();
		
		} else {
			throw new \Exception($exceptionText);
		}
		
	}
	
	/*protected function getResourceUrlFromEntityName($entityName) {
		
		$getControllerAction = 'Mimazoo\\SoaBundle\\Controller\\' . ucfirst($entityName) . 'Controller::getAction';
		
		$c = $this->get('router')->getRouteCollection()->all();
		
		foreach($c as $routeName => $routeObj) {
			if ($getControllerAction === $routeObj->getDefaults()['_controller']) {
				return $routeName;
			}
		}
		
		throw new \Exception('Resource u');
	}*/
	
	
	/**
	* @QueryParam(name="page", requirements="\d+", default="1", description="Page returned")
	* @QueryParam(name="pageStartIndex", requirements="\d+", description="Page start index for paging.")
	* @QueryParam(name="pageSize", requirements="\d+", default="2", description="Maximum number of results on one page.")
	* @QueryParam(name="sortBy", requirements="[a-zAZ.]+", description="Sort by column")
	* @QueryParam(name="sortOrder", requirements="^(asc|desc)$", strict=true, nullable=true, description="Sort by column")
	*/
	protected function getPagingParams(){
		$pagingParamFetcher = $this->container->get('mimazoo_soa.request.param_fetcher');
		$pagingParamFetcher->setController(array($this, __FUNCTION__));
		return $pagingParamFetcher->all();
	}
	
	/**
	 * Add paging links like next, prev, last...
	 * 
	 * @param Hal $hal
	 * @param array $paginationData
	 */
	private function addPagingLinks(Hal $hal, $paginationData) {
		
		$request = $this->getRequest();
		
		$queryParams = array();
		
		if ($request->get('expand')) {
			$queryParams['expand'] = $request->get(self::EXPAND_PARAM_NAME);
		}
		
		if ($request->get('sortBy')) {
			$queryParams['sortBy'] = $request->get('sortBy');
		}
			
		if ($request->get('sortOrder')) {
			$queryParams['sortOrder'] = $request->get('sortOrder');
		}
			
		if ($request->get('pageSize')) {
			$queryParams['pageSize'] = $request->get('pageSize');
		}
		
		//If there is a profiler and has any records
		if($this->get('profiler') != null){	  
		    if(count($this->get('profiler')->find('', '', 1, '', '', '')) > 0){ 
    		    $profileUrl = $this->generateUrl(
    		                '_profiler',
    		                array('token' => $this->get('profiler')->find('', '', 1, '', '', '')[0]['token']),
    		                true
    		    );
    		    
    		    $hal->addLink('profiler', $profileUrl, 'profiler');
		    }
		}
		
		if (isset($paginationData['previous'])) {
			$this->addPagingLink($hal, 'prev', $paginationData['previous'], $queryParams);
		}
		
		if (isset($paginationData['next'])) {
			$this->addPagingLink($hal, 'next', $paginationData['next'], $queryParams);
		}
		
		if (isset($paginationData['pagesInRange'])) {
			
			foreach ( $paginationData['pagesInRange'] as $page) {
				$this->addPagingLink($hal, 'pages', $page, $queryParams, $page);
			}
			
		}
		
		$this->addPagingLink($hal, 'first', $paginationData['first'], $queryParams);
			
		$this->addPagingLink($hal, 'last', $paginationData['last'], $queryParams);
		
	}
	
	/**
	 * Add one paging link like next, prev...
	 * 
	 * @param Hal $hal
	 * @param string $nameString
	 * @param integer $page
	 * @param array $queryParams
	 */
	private function addPagingLink($hal, $nameString, $page, $queryParams, $title = null)
	{
		$prevUrl = $this->generateUrl(
				$this->getRequest()->get('_route'),
				array('page' => $page) + $queryParams + $this->getWildcardParams(),
				true
		);
		
		$hal->addLink($nameString, $prevUrl, $title);
	}
	
	/**
	 * Setter for wildcard params
	 * 
	 * @param array $wildcardParams
	 * @throws \Exception
	 * @return boolean
	 */
	public function setWildcardParams($wildcardParams = NULL) {

		//testing option of setting params
		if (NULL !== $wildcardParams) {
			
			if (!is_array($wildcardParams)) {
				throw new \Exception('Wildcard params should be of array type.');				
			}
			
			$this->wildcardParams = $wildcardParams;
			
			return true;
		}
		
		//set them from current route
		$this->wildcardParams = array();
		
		$variables = $this->getWildcardParamNamesForRouteName($this->getRequest()->get('_route'));
		
		foreach ($variables as $variable) {
			if ('_' !== substr($variable,0,1)) {
				$this->wildcardParams[$variable] = $this->getRequest()->get($variable);
			}
		}
		
	}
	
	/**
	 * Wildcard params gettter
	 * 
	 * @return array
	 */
	public function getWildcardParams() {
		return $this->wildcardParams;
	}
	
	/**
	 * Return wildcard parameter names
	 * @param string $route
	 * @return array
	 */
	private function getWildcardParamNamesForRouteName($route = NULL) {
		
		$route = (NULL === $route)?$this->getRequest()->get('_route'):$route;
        if(is_object($this->get('router')->getRouteCollection()->get($route)))
		    $variables = $this->get('router')->getRouteCollection()->get($route)->compile()->getVariables();
        else
            $variables = array();
		
		return $variables;
	}
	
	/**
	 * $resourceName getter
	 * If it's not set it greps it from class name.
	 * 
	 * @return string
	 */
	public function getResourceName() {
		
		if (!empty($this->resourceName)) {
			return $this->resourceName;
		}

		preg_match( '/(\w*)Controller$/' , get_class($this), $matches);
		return strtolower($matches[1]);
	}
	
	/**
	 * $resourceName setter
	 */
	public function setResourceName($resourceName) {
		$this->resourceName = $resourceName;
	}
	
	
	/**
	 * Remove unexpected fields from input data
	 * 
	 * @param Form $form
	 * @param Request $request
	 */
	protected function filterOutUnexpectedFields(Form $form, Request $request) {
		
		//get form fields that are expected
		$expectedFormFields = array_keys($form->all());
		
		//loop through request values and remove not expected ones
		foreach ($request->request->all() as $param => $value) {
			if (!in_array($param, $expectedFormFields)) {
				$request->request->remove($param);
			}
		}
		
	}
	
	/**
	 * Update or create new item
	 *
	 * @param object $entity
	 */
	protected function processForm($entity) {

		$resourceName = $this->getResourceName();
		
		$new = (NULL === $entity->getId())?true:false;
	
		$typeClass = '\\Mimazoo\\SoaBundle\\Form\\Type\\' . ucfirst($resourceName) . 'Type';

		
		$form = $this->createForm(new $typeClass(), $entity);
		
		$request = $this->getRequest();
		
		//check if there are any fields that are not expected and remove them
		//like _links... so that we can send the same object as we GET
		$this->filterOutUnexpectedFields($form, $request);
		
		//include form data in parent parameter with the form name
		$request->request = new ParameterBag(array($resourceName => $request->request->all()));
		$form->bind($this->getRequest());
		
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			
			try {
				$em->flush();
			} catch (DBALException  $e) {
				//print_r($e->getMessage());die('foo');
				return $this->view($e->getMessage(), 400);
			}
			
			//return the Location header with the new resource
			if ($new) {
				return $this->view()->setHeader('Location',
						$this->getResourceUrl($entity)
				);
			}
			 
		} else {
			//input data not valid, output error details
			return $this->view($form, 400);
		}
		 
	}
	
	/**
	 * Patch update fields
	 * 
	 * @param object $entity
	 * @todo Support patching of transformed form params
	 */
	protected function patch($entity)
	{
		
		if (empty($this->patchWhitelist)) {
			return $this->view(array('errors' => array('No field is allowed to be patch updated.')), 400);
		}
		
		$parameters = array();
		 
		foreach ($this->getRequest()->request->all() as $k => $v) {
		
			// whitelist
			if (in_array($k, $this->patchWhitelist)) {
				$parameters[$k] = $v;
			}
		
		}
		
		if (0 === count($parameters)) {
			return $this->view(array('errors' => array('Invalid parameters.')), 400);
		}
		
		$entity->fromArray($parameters);
		
		$errors = $this->get('validator')->validate($entity);
		
		if (0 < count($errors)) {
			return $this->view(array('errors' => $errors), 400);
		}
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($entity);
		
		try {
			$em->flush();
		} catch (\Exception  $e) {
			return $this->view($e->getMessage(), 400);
		}
		
	}
	
	
	/**
	 * Sets the Container associated with this Controller.
	 *
	 * @param ContainerInterface $container A ContainerInterface instance
	 *
	 * @api
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		parent::setContainer($container);
		Hal::setSerializer($this->get('serializer'));
		Hal::setExpandOptions($this->getExpandOptionsFromMetadata());
		
		$this->setWildcardParams();

	}
	
	public function getExpandOptionsFromMetadata(){
		
		return true;
		
		//replace with auto generating list from Entity dir
		$entities = array(
			'provider',	
			'real'
		);
		
		$apiExpandOptions = array();
		
		$serializerMetadata = $this->get('jms_serializer.metadata_factory');
		
		foreach ($entities as $entity) {
			$entityMetadata = $serializerMetadata->getMetadataForClass('Mimazoo\\SoaBundle\\Entity\\' . ucfirst($entity));
			$apiExpandOptions[$entity] = $this->getExpandParamsFromEntityMetadata($entityMetadata);
		}
		
		return $apiExpandOptions;
	}
	
	private function getExpandParamsFromEntityMetadata($entityMetadata) {
		
		$entityExpandArr = array();
		
		foreach ($entityMetadata->propertyMetadata as $data) {
			$entityExpandArr = array_merge($entityExpandArr, $this->getExpandGroupsFromGroupsArray($data->groups));
		}
		
		return $entityExpandArr;
	}
	
	private function getExpandGroupsFromGroupsArray($groupsArr) {
		
		if (empty($groupsArr)) {
			return array();
		}
		
		$fieldGroupsArr = array();
		
		foreach ($groupsArr as $group) {
			if (1 === preg_match('/^expand_([a-zA-Z]+)/', $group, $matches) ) {
				$fieldGroupsArr[] = $matches[0];
			}
		}
		
		return $fieldGroupsArr;
	}
	
	
}