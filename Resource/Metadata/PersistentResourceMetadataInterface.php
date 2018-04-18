<?php

namespace MNC\Bundle\RestBundle\Resource\Metadata\Metadata;

/**
 * Interface PersistentResourceMetadataInterface
 * @package MNC\Bundle\RestBundle\Resource\Mapping\Metadata
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface PersistentResourceMetadataInterface
{
    /**
     * @return string
     */
    public function getUriPathName();

    /**
     * @return string
     */
    public function getIdentifierFieldName();

    /**
     * @return string
     */
    public function getClassName();

    /**
     * @return string
     */
    public function getPersistence();
}