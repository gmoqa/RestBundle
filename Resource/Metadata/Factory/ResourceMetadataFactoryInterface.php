<?php

namespace MNC\Bundle\RestBundle\Resource\Metadata\Factory;
use MNC\Bundle\RestBundle\Resource\Metadata\Metadata\PersistentResourceMetadataInterface;

/**
 * Interface ResourceMappingInformationInterface
 * @package MNC\Bundle\RestBundle\Resource\Mapping
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface ResourceMetadataFactoryInterface
{
    /**
     * @param string $uriName
     * @return PersistentResourceMetadataInterface
     */
    public function getResourceMetadata(string $uriName);
}