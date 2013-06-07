<?php

namespace Mimazoo\SoaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', null, array('mapped' => false))
            ->add('fbId')
            ->add('fbAccessToken')
            ->add('applePushToken')
            ->add('name')
           	->add('slug')
            ->add('distanceBest')
            ->add('challengesCounter')
            ->add('created', null, array('mapped' => false))
            ->add('updated', null, array('mapped' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mimazoo\SoaBundle\Entity\Player',
        	'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return 'player';
    }
}