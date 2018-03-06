<?php

namespace MNC\Bundle\RestBundle\Filter\Resolver;
use MNC\Bundle\RestBundle\Filter\FilterInterface;

/**
 * Class FilterResolver
 * @package MNC\Bundle\RestBundle\Filter\Resolver
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class FilterResolver implements FilterResolverInterface
{
    /**
     * @param FilterInterface[] $filters
     */
    public function applyFilters($data, $filters)
    {
        // TODO: Check in what format the data comes. It is either a query builder,
        // an Array Collection, a PersistentCollection or a
        foreach ($filters as $filter) {

        }
        // TODO: Loop over the filters and verify supported types and all that.
    }



}