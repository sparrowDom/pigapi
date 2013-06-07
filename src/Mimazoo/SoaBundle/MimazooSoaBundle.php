<?php

namespace Mimazoo\SoaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;
use Mimazoo\SoaBundle\DependencyInjection\Factory\PointHandlerFactory;
use Mimazoo\SoaBundle\DependencyInjection\Factory\PolygonHandlerFactory;
use Mimazoo\SoaBundle\DependencyInjection\Factory\TimeRangeHandlerFactory;
use Mimazoo\SoaBundle\DependencyInjection\Compiler\OverrideServiceCompilerPass;

class MimazooSoaBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);
		$container->addCompilerPass(new OverrideServiceCompilerPass());
	}
}
