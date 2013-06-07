<?php

namespace Mimazoo\SoaBundle\EventListener;

use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener as OriginalResizeFormListener;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class ResizeFormListener extends OriginalResizeFormListener
{
	
	private $isDataNull = false;

	public function preSetData(FormEvent $event)
	{
		$form = $event->getForm();
		$data = $event->getData();
		
		if (null === $data) {
			//$data = array();
			return true;
		}
	
		if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
			throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
		}
	
		// First remove all rows
		foreach ($form as $name => $child) {
			$form->remove($name);
		}
	
		// Then add all rows again in the correct order
		foreach ($data as $name => $value) {
			$form->add($this->factory->createNamed($name, $this->type, null, array_replace(array(
					'property_path' => '['.$name.']',
			), $this->options)));
		}
	}
	
	public function preBind(FormEvent $event)
	{
		
		$form = $event->getForm();
		$data = $event->getData();
		
		if (null === $data || '' === $data) {
			$this->isDataNull = true;
			$data = array();
		}
	
		if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
			throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
		}
	
		// Remove all empty rows
		if ($this->allowDelete) {
			foreach ($form as $name => $child) {
				if (!isset($data[$name])) {
					$form->remove($name);
				}
			}
		}
	
		// Add all additional rows
		if ($this->allowAdd) {
			foreach ($data as $name => $value) {
				if (!$form->has($name)) {
					$form->add($this->factory->createNamed($name, $this->type, null, array_replace(array(
							'property_path' => '['.$name.']',
					), $this->options)));
				}
			}
		}
	}
	
    public function onBind(FormEvent $event)
    {
    	
        $form = $event->getForm();
        $data = $event->getData();

        //set data to null to prevent check and update if it is not needed
        if ($this->isDataNull) {
        	$event->setData(null);
        	return true;
        }
        
        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

        // The data mapper only adds, but does not remove items, so do this
        // here
        if ($this->allowDelete) {
            foreach ($data as $name => $child) {
                if (!$form->has($name)) {
                    unset($data[$name]);
                }
            }
        }

        $event->setData($data);
    }
}
