<?php

namespace MNC\Bundle\RestBundle\Exception;

use MNC\ProblemDetails\ApiException;

/**
 * Class ResourceException
 * @package MNC\Bundle\RestBundle\Exception
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class ResourceException extends ApiException
{
    const TYPE = '/errors/resource';
    const TITLE = 'Resource error.';
    const STATUS = 400;

    public function __construct(string $detail = '', array $extra = [], \Throwable $previous = null)
    {
        parent::__construct(self::TYPE, self::TITLE, self::STATUS, $detail, $extra, $previous);
    }

    /**
     * @return \MNC\ProblemDetails\ApiExceptionInterface
     */
    public static function noResultsFound()
    {
        $instance  = new self(
            sprintf('No results could be found.')
        );
        return $instance->setStatusCode(404);
    }
}