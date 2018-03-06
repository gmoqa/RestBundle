<?php

namespace MNC\Bundle\RestBundle;

use MNC\Bundle\RestBundle\DependencyInjection\Compiler\ExtensionCompilerPass;
use MNC\Bundle\RestBundle\DependencyInjection\Compiler\PublicServicesCompilerPass;
use MNC\Bundle\RestBundle\DependencyInjection\Compiler\TransformerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MNCRestBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TransformerCompilerPass());
        $container->addCompilerPass(new ExtensionCompilerPass());
        $container->addCompilerPass(new PublicServicesCompilerPass());
        parent::build($container);
    }
}
