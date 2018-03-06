<?php

namespace MNC\Bundle\RestBundle\DependencyInjection\Compiler;

use Limenius\Liform\Transformer\TransformerInterface;
use MNC\Bundle\RestBundle\Manager\ResourceManagerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class PublicServicesCompilerPass
 * @package MNC\Bundle\RestBundle\DependencyInjection\Compiler
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class PublicServicesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definitions = $container->getDefinitions();

        foreach ($definitions as $definition) {
            if (strpos($definition->getClass(), 'App\Transformer') !== false
                OR strpos($definition->getClass(), 'App\ResourceManager') !== false)
            {
                $definition->setPublic(true);
                $definition->setPrivate(false);
            }
        }
    }

}