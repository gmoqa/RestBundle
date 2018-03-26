<?php

namespace MNC\Bundle\RestBundle\DependencyInjection;

use League\Fractal\TransformerAbstract;
use MNC\Bundle\RestBundle\DependencyInjection\Compiler\DoctrineFilterCompilerPass;
use MNC\Bundle\RestBundle\DependencyInjection\Compiler\EntityFactoryCompilerPass;
use MNC\Bundle\RestBundle\DoctrineFilter\DoctrineFilterInterface;
use MNC\Bundle\RestBundle\EntityFactory\FactoryDefinitionInterface;
use MNC\Bundle\RestBundle\EventSubscriber\FilterSubscriber;
use MNC\Bundle\RestBundle\Manager\AbstractResourceManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MNCRestExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('liform_transformers.yml');

        $container->registerForAutoconfiguration(TransformerAbstract::class)
            ->addTag('mnc_rest.transformer')
            ->setPublic(true);

        $container->registerForAutoconfiguration(AbstractResourceManager::class)
            ->addTag('mnc_rest.resource_manager')
            ->setPublic(true);

        $container->registerForAutoconfiguration(DoctrineFilterInterface::class)
            ->addTag(DoctrineFilterCompilerPass::TAG);

        $container->registerForAutoconfiguration(FactoryDefinitionInterface::class)
            ->addTag(EntityFactoryCompilerPass::TAG);
    }
}
