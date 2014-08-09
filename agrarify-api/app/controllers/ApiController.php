<?php

use Agrarify\Api\Exception\ApiErrorException;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;

class ApiController extends BaseController {

    /**
     * @return array
     * @throws Agrarify\Api\Exception\ApiErrorException
     */
    protected function getRequestPayloadItem()
    {
        $payload = Request::all();
        if (is_array($payload) and array_key_exists('item', $payload))
        {
            return $payload['item'];
        }

        throw new ApiErrorException(HttpResponse::HTTP_BAD_REQUEST, [
            'message' => 'Unable to find or parse request payload. Is your Content-Type set correctly?'
        ]);
    }

    /**
     * @param int $http_status HTTP status code
     * @param array $errors Array of errors, where each error is of form ['message' => '', 'code' => 111]
     *
     * @return Response
     */
    protected function sendErrorResponse($http_status, $errors = [])
    {
        // if we've been given a single error, transform it into an array of one error element
        if (!isset($errors[0]) or !is_array($errors[0]))
        {
            $errors = [$errors];
        }

        // ensure that a code is assigned to each error
        $transformed_errors = [];

        foreach ($errors as $error)
        {
            if (!isset($error['code']))
            {
                $error['code'] = ApiErrorException::ERROR_CODE_NO_CODE_ASSIGNED;
            }

            $transformed_errors[] = ['message' => $error['message'], 'code' => $error['code']];
        }

        // send the response
        return Response::json(['errors' => $transformed_errors], $http_status);
    }

    /**
     * @param array $errors Array of errors, where each error is of form ['message' => '', 'code' => 111]
     * @return Response
     */
    protected function sendErrorForbiddenResponse($errors = [])
    {
        return $this->sendErrorResponse(HttpResponse::HTTP_FORBIDDEN, $errors);
    }

}
