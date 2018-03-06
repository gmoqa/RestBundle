<?php

namespace MNC\Bundle\RestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Trait NoIndexActionTrait
 * @package MNC\Bundle\RestBundle\Controller
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
trait NoIndexActionTrait
{
    /**
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $route = $request->getPathInfo();
        throw new RouteNotFoundException("Route $route not found");
    }
}