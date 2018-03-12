<?php

namespace MNC\Bundle\RestBundle\DependencyInjection\Compiler;

use MNC\Bundle\RestBundle\EntityFactory\FactoryDefinitionLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EntityFactoryCompilerPass implements CompilerPassInterface
{
    const TAG = 'mnc_rest.factory_definition';

    public function process(ContainerBuilder $container)
    {
        $loaderService = $container->getDefinition(FactoryDefinitionLoader::class);
        $factoryDefinitions = $container->findTaggedServiceIds(self::TAG);

        $definitions = [];
        foreach ($factoryDefinitions as $serviceId => $tags) {
            $definitions[] = new Reference($serviceId);
        }

        $loaderService->addMethodCall('addDefinitions', [$definitions]);
    }
}