<?php
namespace Mimazoo\SoaBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class StringToBooleanTransformer implements DataTransformerInterface
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

	public function transform($input) 
	{
		return $input;
	}

	public function reverseTransform($input) 
	{
		if ('' === $input) {
			return false;
		} else if ('1' === $input) {
			return true;
		} else {
			throw new TransformationFailedException('Smth wrong with input data.');
		}
		
	}
	
}