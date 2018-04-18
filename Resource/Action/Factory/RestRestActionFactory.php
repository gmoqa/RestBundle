<?php

namespace MNC\Bundle\RestBundle\Resource\Action\Factory;

use MNC\Bundle\RestBundle\Request\Parser\RestRequestParser;
use MNC\Bundle\RestBundle\Resource\Metadata\Factory\ResourceMetadataFactory;
use MNC\Bundle\RestBundle\Rest\Info\RestInfo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class RestRestActionFactory
 * @package MNC\Bundle\RestBundle\Resource\Action
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class RestRestActionFactory implements RestActionFactoryInterface
{
    /**
     * @var RestRequestParser
     */
    private $requestParser;
    /**
     * @var ResourceMetadataFactory
     */
    private $mappingInformation;

    /**
     * RestRestActionFactory constructor.
     * @param RestRequestParser       $requestParser
     * @param ResourceMetadataFactory $mappingInformation
     */
    public function __construct(RestRequestParser $requestParser, ResourceMetadataFactory $mappingInformation)
    {
        $this->requestParser = $requestParser;
        $this->mappingInformation = $mappingInformation;
    }

    public function createRestAction(Request $request)
    {
        // We parse the request blocks into objects
        $pathInfo = $this->requestParser->parse($request->getPathInfo());

        // We check if the mapping info exists, and then fetch it, modify it and put it into the PersistentResourceMetadata.
        $metas = [];
        foreach ($pathInfo as $block) {
            $meta = $this->mappingInformation->getResourceMetadata($block->getResourceName());
            if ($meta === null) {
                throw new NotFoundHttpException(sprintf('Resource name %s does not exist or is not registered', $block->getResourceName()));
            }
            $meta->setIdentifierValue($block->getResourceId());
            $metas[] = $meta;
        }
        // We need to check if we are dealing with multiple resources or just one.
        if ($pathInfo->hasOneBlock()) {
            $action = new RestInfo($metas[0]);
        } else {
            $action = new RestInfo(array_pop($metas), $metas);
        }
        $action->initializeRequestParams($request);
        dump($action);exit;


        // If the action does not exist, then we search for it in the action registry.

        // If no action exist, then we throw a 404.
    }
}