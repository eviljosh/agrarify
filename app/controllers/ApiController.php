<?php

use Agrarify\Api\Exception\ApiErrorException;
use Agrarify\Transformers\AgrarifyTransformer;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

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
    protected function assertRequestPayloadItem()
    {
        $payload = Request::all();
        if (is_array($payload) and array_key_exists('item', $payload))
        {
            return $this->transformer->transformInput($payload['item']);
        }

        throw new ApiErrorException(
            ['message' => 'Unable to find or parse request payload. Is your Content-Type set correctly? Is your JSON properly formatted?'],
            HttpResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @param mixed $payload The Model object or Collection to send
     * @param array $transform_options Optional options to pass to the transformer
     * @param array $metadata Optional metadata to return with the response
     * @param int $http_status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendSuccessResponse($payload, $transform_options = [], $metadata = [], $http_status = HttpResponse::HTTP_OK)
    {
        $json_payload = '';

        if ($payload instanceof \Illuminate\Database\Eloquent\Model)
        {
            $json_payload = [
                $this->transformer->getSingularName() => $this->transformer->transform($payload, $transform_options)
            ];
        }
        else
        {
            $json_payload = [
                $this->transformer->getPluralName() => $this->transformer->transformCollection($payload, $transform_options)
            ];
        }
        if (!empty($metadata))
        {
            $json_payload['metadata'] = $metadata;
        }

        return Response::json($json_payload, $http_status);
    }

    /**
     * @param mixed $payload The Model object or Collection to send
     * @param array $transform_options Optional options to pass to the transformer
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendSuccessResponseCreated($payload, $transform_options = [])
    {
        return $this->sendSuccessResponse($payload, $transform_options, [], HttpResponse::HTTP_CREATED);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendSuccessNoContentResponse()
    {
        return $this->sendSuccessResponse([], [], [], HttpResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param array $errors Array of errors, where each error is of form ['message' => '', 'code' => 111]
     * @param int $http_status HTTP status code
     *
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendErrorForbiddenResponse($errors = [])
    {
        return $this->sendErrorResponse($errors, HttpResponse::HTTP_FORBIDDEN);
    }

    /**
     * @param array $errors Array of errors, where each error is of form ['message' => '', 'code' => 111]
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendErrorUnauthorizedResponse($errors = [])
    {
        return $this->sendErrorResponse($errors, HttpResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @param array $errors Array of errors, where each error is of form ['message' => '', 'code' => 111]
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendErrorBadRequestResponse($errors = [])
    {
        return $this->sendErrorResponse($errors, HttpResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendErrorNotImplementedResponse()
    {
        return $this->sendErrorResponse(['message' => 'Not yet implemented.'], HttpResponse::HTTP_NOT_IMPLEMENTED);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendErrorNotFoundResponse()
    {
        return $this->sendErrorResponse(['message' => 'Specified resource not found.'], HttpResponse::HTTP_NOT_FOUND);
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
            foreach ($e->getValidationErrors() as $field_name => $errors)
            {
                $error_code = ApiErrorException::ERROR_CODE_VALIDATION;

                if ($model instanceof \Agrarify\Models\Accounts\Account)
                {
                    if ($errors[0] == 'The email address has already been taken.')
                    {
                        $error_code = ApiErrorException::ERROR_CODE_EMAIL_ALREADY_TAKEN;
                    }
                    elseif ($errors[0] == 'The email address must be a valid email address.')
                    {
                        $error_code = ApiErrorException::ERROR_CODE_EMAIL_INVALID;
                    }
                }

                $message = $field_name . ' - ' . $errors[0];
                $messages[] = ['message' => $message, 'code' => $error_code];
            }

            throw new ApiErrorException($messages, HttpResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return Agrarify\Models\Accounts\Account
     */
    protected function getAccount()
    {
        return Session::get('account');
    }

    /**
     * @return Agrarify\Models\Oauth2\OauthAccessToken
     */
    protected function getAccessToken()
    {
        return Session::get('access_token');
    }

}
