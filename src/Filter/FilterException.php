<?php

namespace MNC\Bundle\RestBundle\Filter;

use MNC\Bundle\RestBundle\ApiProblem\ApiProblem;
use MNC\Bundle\RestBundle\ApiProblem\ApiProblemException;

/**
 * Class FilterException
 * @package MNC\Bundle\RestBundle\Filter
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class FilterException extends ApiProblemException
{
    /**
     * @param $string
     * @return FilterException
     */
    public static function invalidBase64($string)
    {
        $detail = sprintf('Invalid base64 string sent in filter.');
        return new self(ApiProblem::create(400, $detail)->set('base64', $string));
    }

    /**
     * @param $error
     * @return FilterException
     */
    public static function invalidJson($error)
    {
        $detail = sprintf('Invalid json string sent in filter.');
        return new self(ApiProblem::create(400, $detail)->set('json_error', $error));
    }

    /**
     * @param $operator
     * @return FilterException
     */
    public static function unsupportedOperator($operator)
    {
        $detail = sprintf('Unsupported operator used for filter', $operator);
        return new self(ApiProblem::create(400, $detail)->set('sent_operator', $operator));
    }

    /**
     * @return FilterException
     */
    public static function invalidOrderParams()
    {
        $detail = sprintf('Invalid order param value sent.');
        return new self(ApiProblem::create(400, $detail));
    }

    /**
     * @return FilterException
     */
    public static function emptyOrderParam()
    {
        $detail = sprintf('Order param cannot be empty.');
        return new self(ApiProblem::create(400, $detail));
    }
}