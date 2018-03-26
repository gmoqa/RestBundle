<?php

namespace MNC\Bundle\RestBundle\DoctrineFilter;

use Doctrine\ORM\QueryBuilder;
use MNC\Bundle\RestBundle\Doctrine\Utils\QueryNameGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface DoctrineFilterInterface
 * @package MNC\Bundle\RestBundle\DoctrineFilter
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface DoctrineFilterInterface
{
    /**
     * The filter name. It will be included in the filter metadata of the response.
     * @return string
     */
    public function getFilterName(): string;

    /**
     * This method should return either true or false if this filter is supported.
     * @param Request $request
     * @param string  $resourceClass
     * @return bool
     */
    public function supports(Request $request, string $resourceClass);

    /**
     * This method should return the param value for the required filter. If
     * the param value is null or is an empty string, the filtering process will
     * be canceled.
     * @param Request $request
     * @return string
     */
    public function getParamValue(Request $request);

    /**
     * This converts an normalizes the filter into an array that will be passed
     * to the getExpression() method, and embeded in the response metadata.
     *
     * @param string $paramValue
     * @return array
     */
    public function getNormalizedFilter(string $paramValue): array;

    /**
     * This method must create the QB expression and return it.
     * @param array  $normalizedFilter
     * @param string $rootAlias
     * @return mixed
     */
    public function getExpression(array $normalizedFilter, string $rootAlias);

    /**
     * Applies the filter.
     * @param QueryBuilder                $query
     * @param QueryNameGeneratorInterface $generator
     * @param object                      $expression
     * @return mixed
     */
    public function filter(QueryBuilder &$query, QueryNameGeneratorInterface $generator, $expression);
}