<?php

namespace Mimazoo\SoaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mimazoo\SoaBundle\Form\DataTransformer\StringToBooleanTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BooleanType extends AbstractType
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
		$transformer = new StringToBooleanTransformer($this->om);
		$builder->addViewTransformer($transformer);
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'invalid_message' => 'Smth wrong with boolean.',
		));
	}
	
    public function getParent()
    {
    	return 'text';
    }
    
    public function getName()
    {
        return 'boolean';
    }
}
