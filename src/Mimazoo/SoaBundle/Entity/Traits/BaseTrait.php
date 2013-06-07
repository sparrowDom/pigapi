<?php

namespace Mimazoo\SoaBundle\Entity\Traits;
use Symfony\Component\DependencyInjection\Container;

trait BaseTrait {

	/**
	 * Set all submitted values at the same time.
	 *
	 * @param field_type $fieldsArr
	 */
	public function fromArray ($fieldsArr)
	{
		foreach ($fieldsArr as $name => $value) {
			$this->$name = $value;
		}
	}
	
	
	/**
	 * Get id in string form for php arrays
	 *
	 * @return string
	 */
	public function getStringId()
	{
		return 'id_' . $this->getId();
	}
	
	/**
	 * @return string
	 */
	public function getLowerCaseEntityName() {
		
		$className = get_class($this);
		return strtolower(substr($className, strrpos($className, '\\') + 1));
	}
	
	/**
	 * Generate HATEOAS links
	 *
	 * @param Container $container
	 * @param boolean $absolute
	 *
	 * @return array
	 */
	public function getLinks(Container $container, $absolute = true) {
		 
		if (false !== $absolute) {
			$absolute = true;
		}
		
		$links = array();
		$links['self'] = array( 'href' => $this->getSelfUrl( $container, $absolute ) );
		return $links;
	}
	
	/**
	 * Prepare route and parameters for self url
	 * 
	 * @return array [{route}, {params}]
	 */
	public function prepareSelfUrl() {
		
		$route =  'get_' .  $this->getLowerCaseEntityName();
		
		$parameters = array(
    		'id' => $this->getId()
    	);
		
		return array($route, $parameters);
	}
	
	/**
	 * Get self url
	 *
	 * @param Container $container
	 * @param boolean $absolute
	 *
	 * @return string
	 */
	public function getSelfUrl(Container $container, $absolute) {
	
		$prepare = $this->prepareSelfUrl();
		 
		if (false !== $absolute) {
			$absolute = true;
		}
		 
		return $container->get('router')->generate($prepare[0], $prepare[1], $absolute);
	
	}
	
}