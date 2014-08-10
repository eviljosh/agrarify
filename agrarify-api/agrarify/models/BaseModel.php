<?php

namespace Agrarify\Models;

use Agrarify\Exception\ValidationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * Class BaseModel
 * @package Agrarify\Models
 *
 * Base class for Agrarify models
 */
class BaseModel extends Model
{
    /**
     * Validation rules for the model. Must be overridden by child classes.
     *
     * @var array
     */
    public static $rules;

    /**
     * Array of validation errors, if any.
     *
     * @var array
     */
    protected $validation_errors = [];

    /**
     * Runs validation rules, sets validation errors.
     *
     * @return bool
     */
    public function isValid()
    {
        $validator = Validator::make($data = $this->toArray(), $this::$rules);

        if ($validator->fails())
        {
            //$this->validation_errors = $validator->messages()->all();
            $this->validation_errors = $validator->messages()->toArray();
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function getValidationErrors()
    {
        return $this->validation_errors;
    }

    /**
     * Checks validity of current model state and throws an exception is not valid.
     *
     * @throws \Agrarify\Exception\ValidationException
     */
    public function assertValid()
    {
        if (!$this->isValid())
        {
            throw new ValidationException($this->getValidationErrors());
        }
    }
}