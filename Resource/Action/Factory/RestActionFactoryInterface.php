<?php

namespace MNC\Bundle\RestBundle\Resource\Action\Factory;

use Symfony\Component\HttpFoundation\Request;

/**
 * This class is responsible for creating a RestInfo and load it into the
 * Request object with the involved resources and their uris and metadata.
 * This RestInfo will hold every piece of data related to REST for the current
 * request.
 * @package MNC\Bundle\RestBundle\Resource\Action
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface RestActionFactoryInterface
{
    /**
     * @param Request $request
     * @return RestInfoInterface
     */
    public function createRestAction(Request $request);
}