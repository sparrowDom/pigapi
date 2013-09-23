<?php

namespace Mimazoo\SoaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mimazoo\SoaBundle\Form\DataTransformer\PointToArrayTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;


class PointType extends AbstractType
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
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$transformer = new PointToArrayTransformer($this->om);
		$builder->addModelTransformer($transformer);
		
		
		$builder
		->add('type')
		->add('coordinates')
		;	
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'invalid_message' => 'Geolocation parameter of type Point specification is invalid.',
		));
	}
	
    public function getParent()
    {
    	return 'form';
    }
    
    public function getName()
    {
        return 'point';
    }
}
