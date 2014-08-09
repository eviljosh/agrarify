<?php

use Agrarify\Api\Exception\ApiErrorException;
use Agrarify\Transformers\AgrarifyTransformer;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class ApiController extends BaseController {

    /**
     * The transformer to be used.  Each child class should set this appropriately.
     *
     * @var AgrarifyTransformer
     */
    protected $transformer;

    /**
     * @return array
     * @throws Agrarify\Api\Exception\ApiErrorException
     */
    protected function getRequestPayloadItem()
    {
        $payload = Request::all();
        if (is_array($payload) and array_key_exists('item', $payload))
        {
            return $this->transformer->transformInput($payload['item']);
        }

        throw new ApiErrorException(
            ['message' => 'Unable to find or parse request payload. Is your Content-Type set correctly?'],
            HttpResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @param mixed $payload The Model object or Collection to send
     * @param int $http_status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendSuccessResponse($payload, $http_status = HttpResponse::HTTP_OK)
    {
        $json_payload = '';

        if ($payload instanceof \Illuminate\Database\Eloquent\Model)
        {
            $json_payload = [
                $this->transformer->getSingularName() => $this->transformer->transform($payload->toArray())
            ];
        }
        else
        {
            $json_payload = [
                $this->transformer->getPluralName() => $this->transformer->transformCollection($payload->toArray())
            ];
        }

        return Response::json($json_payload, $http_status);
    }

    /**
     * @param array $errors Array of errors, where each error is of form ['message' => '', 'code' => 111]
     * @param int $http_status HTTP status code
     *
     * @return Response
     */
    protected function sendErrorResponse($errors = [], $http_status = HttpResponse::HTTP_BAD_REQUEST)
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
        return $this->sendErrorResponse($errors, HttpResponse::HTTP_FORBIDDEN);
    }

    /**
     * @param Agrarify\Models\BaseModel $model
     * @throws Agrarify\Api\Exception\ApiErrorException;
     */
    protected function assertValid($model)
    {
        try
        {
            $model->assertValid();
        }
        catch(Agrarify\Exception\ValidationException $e)
        {
            $messages = [];
            foreach ($e->getValidationErrors() as $error)
            {
                $strArray = explode(' ',$error);
                $field_name = $strArray[1];

                $message = $field_name . ' - ' . $error;
                $messages[] = ['message' => $message, 'code' => ApiErrorException::ERROR_CODE_VALIDATION];
            }

            throw new ApiErrorException($messages, HttpResponse::HTTP_BAD_REQUEST);
        }
    }

}
