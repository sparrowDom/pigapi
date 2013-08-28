<?php
namespace Mimazoo\WebProfilerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        //print_r($container->getServiceIds());die;
        $definition = $container->getDefinition('web_profiler.controller.profiler');
        $definition->setClass('Mimazoo\WebProfilerBundle\Controller\ProfilerController');
        
        $definition = $container->getDefinition('web_profiler.controller.router');
        $definition->setClass('Mimazoo\WebProfilerBundle\Controller\RouterController');
        
        $definition = $container->getDefinition('web_profiler.controller.exception');
        $definition->setClass('Mimazoo\WebProfilerBundle\Controller\ExceptionController');
    }
}