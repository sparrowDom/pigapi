<?php

namespace Mimazoo\SoaBundle\Hal;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Mimazoo\SoaBundle\Controller\Controller;

class Hal extends \Nocarrier\Hal 
{
	/**
	 * @var array
	 */
	protected $links = array();
	
	/**
	 * Jms serializer
	 */
	private static $serializer;
	
	/**
	 * Expand options for all entities
	 * 
	 * @var array
	 */
	private static $expandOptions;
	
	
	/**
	 * Current request expand wishes
	 * 
	 * @var array
	 */
	private $expand = array();
	
    /**
     * Support also Entity objects for data and convert them to array
     *
     * @param string $uri
     * @param array $data
     */
    public function __construct($uri, $data = array(), $collectionOfEntityType = null)
    {
        $this->uri = $uri;
		
        $this->setExpandFromQueryString();
        
        if (!empty($collectionOfEntityType)) {
        	$this->addCollectionLinks($collectionOfEntityType);
        } else if (is_object($data)) {
        	$this->addEntityLinks($data->getLowerCaseEntityName());
        }
        
        //convert objects to array
        if (is_object($data)) {
        	$data = $this->entityToArray($data);
        }
        
        $this->data = $data;
    }
    
    
    /**
     * Set "serializer" service as static param
     * 
     * @param JMS\Serializer\LazyLoadingSerializer $serializer
     */
    public static function setSerializer($serializer) 
    {
    	self::$serializer = $serializer;
    }
    
    /**
     * Set expand options from classes metadata
     * 
     * @param array $options
     */
    public static function setExpandOptions($options)
    {
    	self::$expandOptions = $options;
    }
    
    
    /**
     * Set expand from query param value
     * 
     * @throws HttpException
     */
	private function setExpandFromQueryString()
    {
    	if ( !empty( $_GET[Controller::EXPAND_PARAM_NAME] ) ) {
    		
    		if (!preg_match('/^[a-zA-Z,]+$/', $_GET[Controller::EXPAND_PARAM_NAME])) {
    			throw new HttpException(400, 'Query param ' . $_GET[Controller::EXPAND_PARAM_NAME]. ' not valid!');
    		}
    		
    		$this->expand = explode(',' , $_GET[Controller::EXPAND_PARAM_NAME]);
    	}
    }
    
    /**
     * Select groups (serializer groups) we want to show
     */
    private function setGroupsForSerialization() {
    	
    	$groups = array('always');
    	
    	if (!empty($this->expand)) {
    		foreach($this->expand as $expandField) {
    			$groups[] =  Controller::EXPAND_PARAM_NAME . '_' . $expandField;
    		}
    	}
    	
    	self::$serializer->setGroups($groups);
    }
    
    /**
     * Add common collection links
     * 
     * @param string $entity
     */
    private function addCollectionLinks($entityName) {
    	
    	$this->addExpandLinks($entityName);
    
    	switch ($entityName) {
    		case 'service':
    			$this->addLink('provider', substr($this->uri, 0, strrpos($this->uri, '/')));
    			break;
    		case 'employee':
    			$this->addLink('provider', substr($this->uri, 0, strrpos($this->uri, '/')));
    			break;
    		case 'real':
    			$this->addLink('provider', substr($this->uri, 0, strrpos($this->uri, '/')));
    			break;
    		case 'open':
    			$this->addLink('provider', substr($this->uri, 0, strrpos($this->uri, '/')));
    			break;
    		case 'price':
    			$this->addLink('provider', substr($this->uri, 0, strrpos($this->uri, '/')));
    			break;
    	}
    	
    }
    
    /**
     * Add entity links
     *
     * @param string $entity
     */
    private function addEntityLinks($entityName) {
		
    	$this->addExpandLinks($entityName);
		
    	switch ($entityName) {
    		
    		case 'provider':
    			$this->addLink('services', $this->uri . '/services');
    			$this->addLink('employees', $this->uri . '/employees');
    			$this->addLink('reals', $this->uri . '/reals');
    			$this->addLink('moveables', $this->uri . '/moveables');
    			$this->addLink('opens', $this->uri . '/opens');
    			$this->addLink('prices', $this->uri . '/prices');
				break;  
				
    		case 'player':
    			break;
    			
			case 'real':
				break;
				
			case 'service':
				$this->addLink('prices', $this->uri . '/prices');
				$this->addLink('selectionitems', $this->uri . '/selectionitems');
				break;
				
			case 'user':
				$this->addLink('bookings', $this->uri . '/bookings');
				break;
				
			case 'booking':
				$this->addLink('bookingservices', $this->uri . '/bookingservices');
				break;
				
			case 'bookingservice':
				$this->addLink('bookingserviceselectionitems', $this->uri . '/bookingserviceselectionitems');
				break;
				
    	}
    	
    }
    
    /**
     * Add expand links
     * 
     * @param string $entityName
     */
    private function addExpandLinks($entityName) {

    	if ( !empty( self::$expandOptions[$entityName] ) ) {
    		foreach (self::$expandOptions[$entityName] as $expandOption) {
    			$optionWithStrippedPrefix = substr($expandOption, strlen(Controller::EXPAND_PARAM_NAME) + 1);
    			
    			if (!in_array($optionWithStrippedPrefix, $this->expand)) {
    				$newExpandArr = array_merge($this->expand, array($optionWithStrippedPrefix));
    				$this->addLink(str_replace('_', '-', $expandOption), $this->generateExpandLink($newExpandArr));
    			} 
    		}
    		
    		if (!empty($this->expand)) {
    			$this->addLink('no-expand', $this->generateExpandLink());
    		}
    	}
    }
   
    /**
     * Generate url for expand link
     * 
     * @param array $newExpandArr
     * @return string
     */
    private function generateExpandLink($newExpandArr = array()) {
    	
    	$urlArr = parse_url($this->uri);	
    	
    	$queryArr = array();
    	
    	if (!empty($urlArr['query'])) {
    		parse_str($urlArr['query'], $queryArr);
    	}
    	
    	if (empty($newExpandArr)) {
    		unset($queryArr['expand']);
    	} else {
    		$newExpandStr = implode($newExpandArr, ',');
    		$queryArr['expand'] = $newExpandStr;
    	}
    	
    	$urlArr['query'] = http_build_query($queryArr);
    	return http_build_url($urlArr);
    	
    }
    
    /**
     * Convert entity object to array
     * 
     * @param object $entityObj
     * @throws HttpException
     * @return array
     */
    private function entityToArray($entityObj) {

    	if (!isset(self::$serializer)) {
    		throw new HttpException(500, 'Serializer has to be set if injected data is an object.');
    	}
    	
    	$this->setGroupsForSerialization($entityObj);
    	
    	$dataArr = $this->keysFromSnakeToCamel(json_decode(self::$serializer->serialize($entityObj, 'json'), true));
    	 
    	return $dataArr;
    }
    
    /**
     * Convert snake case field names to camel case names... foo_bar becomes fooBar
     * 
     * @param array $snakeArr
     * @return array
     */
    private function keysFromSnakeToCamel($snakeArr) {
    	
    	$camelArr = array();
    	
    	foreach ($snakeArr as $key => &$value) {
    		
    		if (is_array($value)) {
    			$value = $this->keysFromSnakeToCamel($value);
    		}
    		
    		$camelArr[$this->snakeToCamel($key)] = $value;
    		
    	}
    	
    	return $camelArr;
    }
    
    /**
     * Convert string from snake to camel case and
     * do not change numbers (numeric arrays)
     * 
     * @param string|integer $val
     * @return string|integer
     */
    private function snakeToCamel($val) {

    	//do not convert numeric arrays
    	if (is_numeric($val) || $val === '_links') {
    		return $val;
    	}
    	
    	$val = str_replace(' ', '', ucwords(str_replace('_', ' ', $val)));
    	$val = strtolower(substr($val,0,1)).substr($val,1);
    	return $val;
    }
    
}