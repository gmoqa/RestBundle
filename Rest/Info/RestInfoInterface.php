<?php

namespace MNC\Bundle\RestBundle\Rest\Info;

use MNC\Bundle\RestBundle\Resource\Metadata\Metadata\PersistentResourceMetadata;

/**
 * This class represents a RestfulAction
 * @package MNC\Bundle\RestBundle\Resource\Action
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface RestInfoInterface
{
    /**
     * Returns the main resource being requested.
     * @return PersistentResourceMetadata
     */
    public function getMainResourceMeta();

    /**
     * Returns an array with the parent resources.
     * @return PersistentResourceMetadata[]|null
     */
    public function getParentResourcesMeta();

    /**
     * Returns te method name.
     * @return string
     */
    public function getMethodName(): string;

    /**
     * Checks if the method is the value passed in $method.
     * @param string $method
     * @return boolean
     */
    public function isMethod(string $method);

    /**
     * Return wether the current rest operation will not change the resource representation
     * stored in the server.
     * @return boolean
     */
    public function isMethodSafe(): bool;

    /**
     * Return wether the current rest operation outcome will not change if called
     * multiple times.
     * @return boolean
     */
    public function isMethodIdempotent(): bool;

    /**
     * @return boolean
     */
    public function hasMultipleResources(): bool;

    /**
     * Returns whether the main resource being requested is an action resource,
     * meaning, a registered controller service.
     * @return boolean
     */
    public function isControllerResource(): bool;

    /**
     * Returns wether the main resource being requested is a document, meaning,
     * the representation of a single resource.
     * @return boolean
     */
    public function isDocumentResource(): bool;

    /**
     * Returns wether the main resource involved in the operation is a collection,
     * meaning, the representation of multiple resources.
     * @return boolean
     */
    public function isCollectionResource(): bool;
}