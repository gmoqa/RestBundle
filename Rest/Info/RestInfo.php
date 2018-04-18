<?php

namespace MNC\Bundle\RestBundle\Rest\Info;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use MNC\Bundle\RestBundle\Resource\Metadata\Metadata\PersistentResourceMetadata;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RestInfo
 * @package MNC\Bundle\RestBundle\Resource\Action
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class RestInfo implements RestInfoInterface
{
    /**
     * @var PersistentResourceMetadata
     */
    private $mainResourceMeta;
    /**
     * @var PersistentResourceMetadata[]
     */
    private $parentResourcesMeta = [];
    /**
     * @var string
     */
    private $requestMethod;
    /**
     * @var string
     */
    private $responseContentType;
    /**
     * @var string
     */
    private $requestContentType;

    public function __construct(PersistentResourceMetadata $mainResourceMeta, $parentResourcesMeta = [])
    {
        $this->mainResourceMeta = $mainResourceMeta;
        $this->parentResourcesMeta = $parentResourcesMeta;
    }

    public function initializeRequestParams(Request $request)
    {
        $this->requestMethod = $request->getMethod();
        $this->responseContentType = $request->headers->get('Accept', 'application/json');
        $this->requestContentType = $request->headers->get('Content-Type', 'application/json');
    }

    /** @inheritdoc */
    public function getMainResourceMeta()
    {
        return $this->mainResourceMeta;
    }

    /** @inheritdoc */
    public function getParentResourcesMeta()
    {
        return $this->parentResourcesMeta;
    }

    /** @inheritdoc */
    public function getMethodName(): string
    {
        return $this->requestMethod;
    }

    /** @inheritdoc */
    public function isMethod(string $action)
    {
        return $this->requestMethod === strtoupper($action);
    }

    /** @inheritdoc */
    public function isMethodSafe(): bool
    {
        return
            $this->isMethod('GET') OR
            $this->isMethod('HEAD')  OR
            $this->isMethod('OPTIONS');
    }

    /** @inheritdoc */
    public function isMethodIdempotent(): bool
    {
        return
            $this->isMethod('GET') OR
            $this->isMethod('HEAD') OR
            $this->isMethod('OPTIONS') OR
            $this->isMethod('PUT') OR
            $this->isMethod('DELETE');
    }

    /** @inheritdoc */
    public function hasMultipleResources(): bool
    {
        return !empty($this->parentResourcesMeta);
    }

    /** @inheritdoc */
    public function isDocumentResource(): bool
    {
        return $this->mainResourceMeta->hasIdValue();
    }

    /** @inheritdoc */
    public function isCollectionResource(): bool
    {
        return !$this->mainResourceMeta->hasIdValue();
    }

    /** @inheritdoc */
    public function isControllerResource(): bool
    {
        return $this->mainResourceMeta instanceof ClassMetadataInfo;
    }
}