<?php

namespace MNC\Bundle\RestBundle\DependencyInjection\Compiler;

use MNC\Bundle\RestBundle\EventSubscriber\DoctrineFilterSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DoctrineFilterCompilerPass
 * @package MNC\Bundle\RestBundle\DependencyInjection\Compiler
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class DoctrineFilterCompilerPass implements CompilerPassInterface
{
    const TAG = 'mnc_rest.doctrine_filter';

    public function process(ContainerBuilder $container)
    {
        $subscriber = $container->getDefinition(DoctrineFilterSubscriber::class);
        $filterDefinitions = $container->findTaggedServiceIds(self::TAG);

        $definitions = [];

        foreach ($filterDefinitions as $serviceId => $tags) {
            $definitions[] = new Reference($serviceId);
        }

        $subscriber->addArgument($definitions);
    }
}