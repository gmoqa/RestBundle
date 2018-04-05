<?php

namespace MNC\Bundle\RestBundle\Exception;

use MNC\ProblemDetails\ApiException;

/**
 * Class BodyFormatException
 * @package MNC\Bundle\RestBundle\Exception
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class BodyFormatException extends ApiException
{
    const TYPE = '/errors/request-body';
    const TITLE = 'Invalid request body.';
    const STATUS = 422;

    /**
     * BodyFormatException constructor.
     * @param string          $detail
     * @param array           $extra
     * @param \Throwable|null $previous
     */
    public function __construct(string $detail = '', array $extra = [], \Throwable $previous = null)
    {
        parent::__construct(self::TYPE, self::TITLE, self::STATUS, $detail, $extra, $previous);
    }

    /**
     * @param string $error
     * @return BodyFormatException
     */
    public static function malformedBody(string $error)
    {
        return new self(
            'The request could not be processed due to a malformed request body.',
            ['parser_error' => $error]
        );
    }
}