<?php

namespace MNC\Bundle\RestBundle\Exception;

use MNC\ProblemDetails\ApiException;

class FormValidationException extends ApiException
{
    const TYPE = '/errors/form-validation';
    const TITLE = 'Validation error.';
    const STATUS = 400;
    const DETAIL = 'Your request could not be processed correctly because it contains invalid input. Please correct and resend.';

    /**
     * FormValidationException constructor.
     * @param array           $extra
     * @param \Throwable|null $previous
     */
    public function __construct(array $extra = [], \Throwable $previous = null)
    {
        parent::__construct(self::TYPE, self::TITLE, self::STATUS, self::DETAIL, $extra, $previous);
    }

    /**
     * @param $errors
     * @return FormValidationException
     */
    public static function create($errors)
    {
        return new self(
            ['errors' => $errors]
        );
    }
}