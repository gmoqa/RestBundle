<?php

namespace MNC\Bundle\RestBundle\Filter;

/**
 * Class FilterInterface
 * @package MNC\Bundle\RestBundle\Filter
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface FilterInterface
{
    /**
     * Gets the target DataType of the filter based on the closure function.
     * @return string
     */
    public function getTarget();

    /**
     * @return mixed
     */
    public function applyFilter();
}