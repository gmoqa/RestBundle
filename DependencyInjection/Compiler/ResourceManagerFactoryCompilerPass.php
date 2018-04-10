<?php

namespace MNC\Bundle\RestBundle\DependencyInjection\Compiler;

use MNC\Bundle\RestBundle\Manager\ResourceManagerFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass loads all the Resource Managers into a single service for
 * easy dependency injection.
 * @package MNC\Bundle\RestBundle\DependencyInjection\Compiler
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class ResourceManagerFactoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $managerFactory = $container->getDefinition(ResourceManagerFactory::class);
        $definitions = $container->findTaggedServiceIds('mnc_rest.resource_manager');

        $references = [];
        foreach ($definitions as $id => $tags) {
            $references[$id] = new Reference($id);
        }

        $managerFactory->addArgument($references);
    }
}