<?php

use Agrarify\Models\Accounts\Account;
use Agrarify\Transformers\AccountTransformer;
use Illuminate\Support\Facades\Response;

class AccountProfilesController extends ApiController {

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transformer = new AccountTransformer();
    }

	/**
	 * HTTP GET
     * Display the specified account.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function showForAccount($id)
	{
        if ($id != 'me')
        {
            return $this->sendErrorNotFoundResponse();
        }
        return $this->sendSuccessResponse($this->getAccount());
	}

	/**
	 * HTTP PUT
     * Update the specified account in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        if ($id != 'me')
        {
            return $this->sendErrorNotFoundResponse();
        }

        $payload = $this->assertRequestPayloadItem();
        $account = $this->getAccount();

        // We validate emails as unique, so we need to do a little dance to avoid trying to revalidate the existing one.
        $should_validate_email = true;
        if (!isset($payload['email_address']) or strtolower($payload['email_address']) == strtolower($account->email_address))
        {
            $should_validate_email = false;
        }

        // Update the account instance.
        $account->fill($payload);

        // Handle password updates separately since they must be hashed.
        if (isset($payload['password']) and strlen($payload['password']) > 0)
        {
            $account->hashAndSetPassword($payload['password']);
        }

        // Validate the account instance
        if ($should_validate_email)
        {
            $this->assertValid($account);
        }
        else
        {
            $current_email = $account->email_address;
            $account->email_address = null;
            $this->assertValid($account);
            $account->email_address = $current_email;
        }

        // Save and return the updated account.
        $account->save();
        return $this->sendSuccessResponse($account);
	}

	/**
     * HTTP DELETE
	 * Remove the specified accounts from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        if ($id != 'me')
        {
            return $this->sendErrorNotFoundResponse();
        }
		// TODO: implement, once we know what will need to be done upon account deletion
        return $this->sendErrorNotImplementedResponse();
	}

}
