<?php

namespace Agrarify\Exception;

/**
 * Agrarify validation exception
 */
class ValidationException extends \Exception
{

    /**
     * @var array List of validation errors of form [...]
     */
    private $validation_errors = [];

    public function __construct($validation_errors = [])
    {
        $this->validation_errors = $validation_errors;
    }

    /**
     * @return array
     */
    public function getValidationErrors()
    {
        return $this->validation_errors;
    }
}
