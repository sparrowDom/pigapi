<?php
namespace Mimazoo\SoaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		//an example of how to change service definitions
		//$definition = $container->getDefinition('jms_serializer.metadata.annotation_driver');
		//$definition->setClass('Mimazoo\SoaBundle\Serializer\AnnotationDriver');
	}
}