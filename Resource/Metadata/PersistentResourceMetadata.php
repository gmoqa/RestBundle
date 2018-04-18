<?php

namespace MNC\Bundle\RestBundle\Resource\Metadata\Metadata;

/**
 * Class PersistentResourceMetadata
 * @package MNC\Bundle\RestBundle\Resource\Mapping\Metadata
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class PersistentResourceMetadata implements PersistentResourceMetadataInterface
{
    /**
     * @var string
     */
    private $uriPathName;
    /**
     * @var string
     */
    private $identifierFieldName;
    /**
     * @var string
     */
    private $className;
    /**
     * @var string
     */
    private $persistence;
    /**
     * @var string|null
     */
    private $identifierValue;

    /**
     * PersistentResourceMetadata constructor.
     * @param string $path
     * @param array  $data
     */
    public function __construct(string $path, array $data)
    {
        $this->uriPathName = $path;
        $this->identifierFieldName = $data['uri_id'];
        $this->className = $data['class'];
        $this->persistence = $data['persistence'];
    }

    /**
     * @return int|null|string
     */
    public function getUriPathName()
    {
        return $this->uriPathName;
    }

    /**
     * @return string
     */
    public function getIdentifierFieldName()
    {
        return $this->identifierFieldName;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getPersistence()
    {
        return $this->persistence;
    }

    public function hasIdValue()
    {
        return !empty($this->identifierValue);
    }

    public function setIdentifierValue(?string $value = null)
    {
        $this->identifierValue = $value;
    }

    public function getIdentifierValue()
    {
        return $this->identifierValue;
    }
}