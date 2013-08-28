<?php

namespace Mimazoo\WebProfilerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mimazoo\WebProfilerBundle\DependencyInjection\Compiler\OverrideServiceCompilerPass;

class MimazooWebProfilerBundle extends Bundle
{
    public function getParent()
    {
        return 'WebProfilerBundle';
    }
    
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    
        $container->addCompilerPass(new OverrideServiceCompilerPass());
    }
}
