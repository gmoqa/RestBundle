<?php

namespace MNC\Bundle\RestBundle\Filter;

/**
 * Class FilterException
 * @package MNC\Bundle\RestBundle\Filter
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class FilterException extends \Exception
{
    /**
     * @param $key
     * @throws FilterException
     */
    public static function missingSupportsKey($key)
    {
        throw new self(sprintf('Missing key (%s) for supports() method on Filter.'), $key);
    }
}