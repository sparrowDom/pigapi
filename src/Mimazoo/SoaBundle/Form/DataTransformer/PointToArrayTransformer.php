<?php
namespace Mimazoo\SoaBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mimazoo\SoaBundle\ValueObject\Point;

class PointToArrayTransformer implements DataTransformerInterface
{
	/**
	 * @var ObjectManager
	 */
	private $om;
	
	/**
	 * @param ObjectManager $om
	 */
	public function __construct(ObjectManager $om)
	{
		$this->om = $om;
	}

	/**
	 * Transforms an object (Point) to array.
	 *
	 * @param  Issue|null $issue
	 * @return string
	 */
	public function transform($point)
	{
		if (null === $point) {
			return null;
		}
		
		$pointArr = array();
		$pointArr['type'] = 'Point';
		$pointArr['coordinates'] = array($point->getLatitude(), $point->getLongitude());
		
		return $pointArr;
	}

	/**
	 * Transforms an array to point object.
	 *
	 * @param  array $number
	 * @return Point|null
	 * @throws TransformationFailedException
	 */
	public function reverseTransform($pointArr)
	{
		if (is_array($pointArr) && !array_filter($pointArr)) {
			return null;
		}
		
		if (!is_array($pointArr) 
    		|| !isset($pointArr['type'])
			|| 'Point' !== $pointArr['type']
    		|| !isset($pointArr['coordinates'][0])
			|| !isset($pointArr['coordinates'][1])
		) {
			throw new TransformationFailedException('Invalid point data. Should be an array with type and coordinates');
		}
		
		return new Point($pointArr['coordinates'][0], $pointArr['coordinates'][1]);
	}
	
}