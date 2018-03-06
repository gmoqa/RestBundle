<?php

namespace MNC\Bundle\RestBundle\Filter\Manager;

use MNC\Bundle\RestBundle\Filter\FilterInterface;
use MNC\Bundle\RestBundle\Filter\QBFilter;
use MNC\Bundle\RestBundle\Filter\Resolver\FilterResolverInterface;

/**
 * Class FilterManager
 * @package MNC\Bundle\RestBundle\Filter\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class FilterManager implements FilterManagerInterface
{
    /**
     * @var FilterResolverInterface
     */
    private $filterResolver;
    /**
     * @var FilterInterface[]
     */
    private $filters;

    public function __construct(FilterResolverInterface $filterResolver)
    {
        $this->filterResolver = $filterResolver;
    }

    public function register(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    public function resolve($data)
    {
        return $this->filterResolver->applyFilters($data, $this->filters);
    }
}