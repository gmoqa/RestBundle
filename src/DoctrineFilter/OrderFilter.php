<?php

namespace MNC\Bundle\RestBundle\DoctrineFilter;

use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\QueryBuilder;
use MNC\Bundle\RestBundle\Doctrine\Utils\QueryNameGeneratorInterface;
use MNC\Bundle\RestBundle\Filter\FilterException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrderFilter
 * @package MNC\Bundle\RestBundle\DoctrineFilter
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class OrderFilter implements DoctrineFilterInterface
{
    public function getFilterName(): string
    {
        return 'order';
    }

    public function supports(Request $request, string $resourceClass)
    {
        return $request->query->has('order');
    }

    public function getParamValue(Request $request)
    {
        return $request->query->get('order');
    }

    public function getNormalizedFilter(string $paramValue): array
    {
        $data = explode('.', $paramValue);
        if (sizeof($data) === 2) {
            [$attr, $order] = $data;
            $order = strtoupper($order);
        } elseif (sizeof($data) === 1) {
            if ($data[0] === '') {
                throw FilterException::emptyOrderParam();
            }
            $attr = $data[0];
            $order = 'ASC';
        } else {
            throw FilterException::invalidOrderParams();
        }
        return [$attr => $order];
    }

    public function getExpression(array $normalizedFilter, string $rootAlias)
    {
        foreach ($normalizedFilter as $attr => $order) {
            return new OrderBy($rootAlias.'.'.$attr, $order);
        }
    }

    public function filter(QueryBuilder &$query, QueryNameGeneratorInterface $generator, $normalizedFilter)
    {
        $query->addOrderBy($normalizedFilter);
    }
}