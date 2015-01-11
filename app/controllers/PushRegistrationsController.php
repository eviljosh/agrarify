<?php

use Agrarify\Lib\PushNotificationAdapter;
use Agrarify\Models\Accounts\PushRegistration;
use Agrarify\Transformers\PushRegistrationTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
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
            return $this->sendSuccessResponse($push_registration);
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
        $is_first_registration = ($this->getAccount()->getPushRegistrations()->count() == 0);

        $payload = $this->assertRequestPayloadItem();

        $push_registration = new PushRegistration($payload);
        $push_registration->setAccount($this->getAccount());
        $this->assertValid($push_registration);

        $sns_arn = PushNotificationAdapter::registerDevice($push_registration);
        $push_registration->setSnsArn($sns_arn);

        // TODO move the sending logic into PushRegistration and Account; TODO: Async do this async...
        if ($is_first_registration) {
            $push_registration->sendFormattedMessage('Welcome!', 'Welcome to Agrarify!');
        }

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

    public function test($id)
    {
        $push_registration = PushRegistration::find($id);

        if ($push_registration)
        {
            if ($push_registration->isEnabled()) {
                $message = 'Test message sent by ' . Config::get('agrarify.app_name') . ' at ' . Carbon::now()->toDateTimeString() . ' to token ' . $push_registration->getToken();
                try {
                    $push_registration->sendFormattedMessage('Test Title', $message);
                    return Response::make('attempted to push message: ' . $message);
                } catch (\Exception $e) {
                    return Response::make('got an exception from AWS SNS: ' . $e->getMessage());
                }
            }
            else {
                return Response::make('This push registration is disabled. This normally happens because AWS SNS tells us that GCM believes the push id we are targeting is invalid.  Please delete this record and create a new one.');
            }
        }
        return $this->sendErrorNotFoundResponse();
    }

}
