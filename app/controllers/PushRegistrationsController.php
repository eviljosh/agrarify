<?php

use Agrarify\Lib\PushNotificationAdapter;
use Agrarify\Models\Accounts\PushRegistration;
use Agrarify\Transformers\PushRegistrationTransformer;
use Illuminate\Support\Facades\Response;

class PushRegistrationsController extends ApiController {

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transformer = new PushRegistrationTransformer();
    }

	/**
     * Display all push registrations for the authenticated account.
	 *
	 * @return Response
	 */
	public function index()
	{
        return $this->sendSuccessResponse($this->getAccount()->getPushRegistrations());
	}

    /**
     * Show the given push registration for the authenticated account.
     *
     * @param mixed $id
     * @return Response
     */
    public function show($id)
    {
        $push_registration = $this->getAccount()->getPushRegistrationById($id);

        if ($push_registration)
        {
            $this->sendSuccessResponse($push_registration);
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Create a new push notification for the authenticated account.
     *
     * @return Response
     */
    public function create()
    {
        $payload = $this->assertRequestPayloadItem();

        $push_registration = new PushRegistration($payload);
        $push_registration->setAccount($this->getAccount());
        $this->assertValid($push_registration);

        $sns_arn = PushNotificationAdapter::registerDevice($push_registration);
        $push_registration->setSnsArn($sns_arn);

        // TODO move the sending logic into PushRegistration and Account; TODO: Async do this async...
        $push_registration->sendMessage('Your device is now enabled for push notifications');

        $push_registration->save();

        return $this->sendSuccessResponseCreated($push_registration);
    }

	/**
     * Update the given push registration for the authenticated account.
	 *
     * @param mixed $id
	 * @return Response
	 */
	public function update($id)
    {
        $payload = $this->assertRequestPayloadItem();
        $push_registration = $this->getAccount()->getPushRegistrationById($id);

        if ($push_registration)
        {
            $push_registration->fill($payload);
            $this->assertValid($push_registration);
            $push_registration->save();

            return $this->sendSuccessResponse($push_registration);
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Delete the given push registration for the authenticated account.
     *
     * @param mixed $id
     * @return Response
     */
    public function destroy($id)
    {
        $push_registration = $this->getAccount()->getPushRegistrationById($id);

        if ($push_registration)
        {
            $push_registration->delete();
            return $this->sendSuccessNoContentResponse();
        }
        return $this->sendErrorNotFoundResponse();
    }

}