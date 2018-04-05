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
     * @return ResourceException
     */
    public static function cannotRequestMultipleResources()
    {
        return new self(
            'This endpoint/method does not support referencing multiple comma separated resources.'
        );
    }

    /**
     * @param $resource
     * @return \MNC\ProblemDetails\ApiExceptionInterface
     */
    public static function resourcesNotFound($resource)
    {
        $instance  = new self(
            sprintf('Resources %s not found', $resource)
        );
        return $instance->setStatusCode(404);
    }

    /**
     * @param $resource
     * @return \MNC\ProblemDetails\ApiExceptionInterface
     */
    public static function resourceNotFound($resource)
    {
        $instance  = new self(
            sprintf('Resource %s not found', $resource)
        );
        return $instance->setStatusCode(404);
    }
}