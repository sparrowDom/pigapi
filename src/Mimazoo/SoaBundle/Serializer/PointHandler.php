<?php

/**
 * We specify in this class how to serialize Geo Point object, so it is outputed in a geojson way with
 * type and coordinates.
 * 
 * @author mitja
 *
 */

namespace Mimazoo\SoaBundle\Serializer;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\GraphNavigator;
use Mimazoo\SoaBundle\ValueObject\Point;
use Mimazoo\SoaBundle\Form\DataTransformer\PointToArrayTransformer;

class PointHandler implements SubscribingHandlerInterface
{
	
	protected $pointToArrayTransformer;
	
	public function __construct(PointToArrayTransformer $transformer)
	{
		$this->pointToArrayTransformer = $transformer;
	}
	
	public static function getSubscribingMethods()
	{
		return array(
				array(
						'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
						'format' => 'json',
						'type' => 'Point',
						'method' => 'serializePointToArray',
				),
		);
	}
	
	public function serializePointToArray(JsonSerializationVisitor $visitor, Point $point, array $type)
	{
		$data = $this->pointToArrayTransformer->transform($point);
		return $data;
	}
}
