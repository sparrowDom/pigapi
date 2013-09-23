<?php

/**
 * Adding links for expanded objects
 * 
 * @author mitja
 *
 */

namespace Mimazoo\SoaBundle\Serializer;

use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;

use Symfony\Component\DependencyInjection\Container;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\Event;


class InnerLinksSubscriber implements EventSubscriberInterface
{
	protected $container;
	
	public function __construct($container)
	{
		$this->container = $container;
	}
	
	public static function getSubscribedEvents()
	{
		return array(
			array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'),
		);
	}
	
	//private $history = array();

	public function onPostSerialize(Event $event)
	{
		
		$pattern = '/^Mimazoo\\\\SoaBundle\\\\Entity\\\\([A-Z].*)/';
		$subject = $event->getType()['name'];
		
		
		//check if this is entity object
		if (is_string($subject) && preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE)) {
			$this->addLinks($matches, $event);
		}
		
		if ($event->getType()['name'] === 'Mimazoo\SoaBundle\ValueObject\TimeRange') {
			//\Doctrine\Common\Util\Debug::dump($event->getObject());
			//die('sdgsdg');
		}
		
		
	}
	
	private function addLinks($classNameMatchesArr, Event $event) {
		
		if (method_exists($classNameMatchesArr[0][0],'getLinks')) {
			$object = $event->getObject();
			$event->getVisitor()->addData('_links', $object->getLinks($this->container) );
		}
		
	}
	
}
