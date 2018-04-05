<?php

namespace MNC\Bundle\RestBundle\Exception;

use MNC\ProblemDetails\ApiException;


/**
 * Class FilterException
 * @package MNC\Bundle\RestBundle\Filter
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class FilterException extends ApiException
{
    const TYPE = '/errors/collection-filtering';
    const TITLE = 'Collection filtering error.';
    const STATUS = 400;

    /**
     * FilterException constructor.
     * @param string          $detail
     * @param array           $extra
     * @param \Throwable|null $previous
     */
    public function __construct(string $detail = '', array $extra = [], \Throwable $previous = null)
    {
        parent::__construct(self::TYPE, self::TITLE, self::STATUS, $detail, $extra, $previous);
    }

    /**
     * @param string $base64
     * @return FilterException
     */
    public static function invalidBase64(string $base64)
    {
        return new self(
        'Invalid base64 string sent in filter.',
            ['sent_base64' => $base64]
        );
    }

    /**
     * @param string $error
     * @return FilterException
     */
    public static function invalidJson(string $error)
    {
        return new self(
            'Invalid json string sent in filter.',
            ['parser_error' => $error]
        );
    }

    /**
     * @param string $operator
     * @return FilterException
     */
    public static function unsupportedOperator(string $operator)
    {
        return new self(
            'Unssuported operator used for filter.',
            ['sent_operator' => $operator]
        );
    }

    /**
     * @return FilterException
     */
    public static function invalidOrderParams()
    {
        return new self(
            'Invalid order param value sent.'
        );
    }

    /**
     * @return FilterException
     */
    public static function emptyOrderParam()
    {
        return new self(
            'Order param cannot be empty.'
        );
    }
}