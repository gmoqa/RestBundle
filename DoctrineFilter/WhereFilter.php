<?php

namespace MNC\Bundle\RestBundle\DoctrineFilter;

use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use MNC\Bundle\RestBundle\Doctrine\Utils\QueryNameGeneratorInterface;
use MNC\Bundle\RestBundle\Exception\FilterException;
use Symfony\Component\HttpFoundation\Request;

class WhereFilter implements DoctrineFilterInterface
{
    public function getFilterName(): string
    {
        return 'where';
    }

    public function supports(Request $request, string $resourceClass)
    {
        return $request->query->has('where');
    }

    public function getParamValue(Request $request)
    {
        return $request->query->get('where');
    }

    public function getNormalizedFilter(string $paramValue): array
    {
        $array = json_decode($paramValue, true);

        if ($array === null) {
            $error = json_last_error_msg();
            throw FilterException::invalidJson($error);
        }
        return $array;
    }

    public function getExpression(array $normalizedFilter, string $rootAlias)
    {
        $expressions = [];
        foreach ($normalizedFilter as $attribute => $rule) {
            foreach ($rule as $operator => $value) {
                $expressions[] = $this->createExpression($rootAlias.'.'.$attribute, $operator, $value);
            }
        }
        return $expressions;
    }

    public function filter(QueryBuilder &$query, QueryNameGeneratorInterface $generator, $expressions)
    {
        foreach ($expressions as $expression) {
            $query->andWhere($expression);
        }
    }

    private function createExpression($field, $operator, $value)
    {
        switch ($operator) {
            case '<':
                return new Comparison($field, Comparison::LT, $value);
            case '<=':
                return new Comparison($field, Comparison::LTE, $value);
            case '>':
                return new Comparison($field, Comparison::GT, $value);
            case '>=':
                return new Comparison($field, Comparison::GTE, $value);
            case '=':
                return new Comparison($field, Comparison::EQ, $value);
            case '<>':
                return new Comparison($field, Comparison::NEQ, $value);
            default:
                throw FilterException::unsupportedOperator($operator);
        }
    }
}