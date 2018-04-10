<?php

namespace MNC\Bundle\RestBundle\Manager;

/**
 * Interface ResourceManagerFactoryInterface
 * @package MNC\Bundle\RestBundle\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface ResourceManagerFactoryInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param $id
     * @return ResourceManagerInterface
     */
    public function get($id): ResourceManagerInterface;

    /**
     * @param $id
     * @return bool
     */
    public function has($id): bool;
}