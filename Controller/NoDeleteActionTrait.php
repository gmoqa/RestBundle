<?php

namespace MNC\Bundle\RestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Trait NoDeleteActionTrait
 * @package MNC\Bundle\RestBundle\Controller
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
trait NoDeleteActionTrait
{
    /**
     * @param Request $request
     * @param $id
     */
    public function deleteAction(Request $request, $id)
    {
        $route = $request->getPathInfo();
        throw new RouteNotFoundException("Route $route not found");
    }
}