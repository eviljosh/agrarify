<?php

namespace Agrarify\Api\Exception;

use Illuminate\Http\Response as HttpResponse;

/**
 * Agrarify API error exception
 */
class ApiErrorException extends \Exception
{
    const ERROR_CODE_NO_CODE_ASSIGNED = 999;

    /**
     * @var array List of errors of form [ ['message' => 'message', 'code' => 'number'], ...]
     */
    var $errors = [];

    /**
     * @var int HTTP status code desired for response
     */
    var $http_status;

    /**
     * @param array $errors Array of errors, where each error is of form ['message' => '', 'code' => 111]
     * @param int $http_status HTTP status code
     */
    public function __construct($errors = [], $http_status = HttpResponse::HTTP_BAD_REQUEST)
    {
        // if we've been given a single error, transform it into an array of one error element
        if (!isset($errors[0]) or !is_array($errors[0]))
        {
            $errors = [$errors];
        }

        $this->http_status = $http_status;
        $this->errors = $errors;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->http_status;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        $transformed_errors = [];

        foreach ($this->errors as $error)
        {
            if (!isset($error['code']))
            {
                $error['code'] = self::ERROR_CODE_NO_CODE_ASSIGNED;
            }

            $transformed_errors[] = ['message' => $error['message'], 'code' => $error['code']];
        }

        return ['errors' => $transformed_errors];
    }
}
