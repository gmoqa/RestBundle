<?php

namespace MNC\Bundle\RestBundle\Resource\Metadata\Factory;

use MNC\Bundle\RestBundle\Resource\Metadata\Metadata\PersistentResourceMetadata;

/**
 * This service uses the bundle configuration to create some sort of metadata
 * for the rest resources configured in the bundle.
 * @package MNC\Bundle\RestBundle\Resource\Mapping
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class ResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    /**
     * @var PersistentResourceMetadata[]
     */
    private $persistenResources;

    /**
     * ResourceMappingInformation constructor.
     * @param array $mapping
     */
    public function __construct(array $mapping = [])
    {
        $this->persistenResources = $this->createMappingInfo($mapping);
    }

    /**
     * @param array $mapping
     * @return PersistentResourceMetadata[]
     */
    private function createMappingInfo(array $mapping = [])
    {
        $this->persistenResources = [];
        foreach ($mapping as $key => $value) {
            $this->persistenResources[$key] = new PersistentResourceMetadata($key, $value);
        }
        return $this->persistenResources;
    }

    /**
     * @param string $uriName
     * @return PersistentResourceMetadata|null
     */
    public function getResourceMetadata(string $uriName)
    {
        if (key_exists($uriName, $this->persistenResources)) {
            return $this->persistenResources[$uriName];
        }
        return null;
    }
}