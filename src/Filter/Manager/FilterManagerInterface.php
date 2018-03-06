<?php

namespace MNC\Bundle\RestBundle\Filter\Manager;

use MNC\Bundle\RestBundle\Filter\FilterInterface;

/**
 * Interface FilterManagerInterface
 * @package MNC\Bundle\RestBundle\Filter\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface FilterManagerInterface
{
    /**
     * @param FilterInterface $filter
     * @return mixed
     */
    public function register(FilterInterface $filter);

    /**
     * @param $data
     * @return mixed
     */
    public function resolve($data);
}