<?php

use Agrarify\Models\Accounts\Account;
use Agrarify\Transformers\AccountTransformer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class AccountsController extends ApiController {

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
	public function show($id)
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
        $preexisting_email = $account->getEmailAddress();

        // We validate emails as unique, so we need to do a little dance to avoid trying to revalidate the existing one.
        $should_validate_email = true;
        if (!isset($payload['email_address']) or strtolower($payload['email_address']) == strtolower($account->getEmailAddress()))
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
            $current_email = $account->getEmailAddress();
            $account->setEmailAddress(null);
            $this->assertValid($account);
            $account->setEmailAddress($current_email);
        }

        if ($preexisting_email != $account->getEmailAddress()) // TODO: refactor this to only have one boolean throughout
        {
            $account->setVerificationCode('');
        }

        // Save and return the updated account.
        $account->save();

        if ($preexisting_email != $account->getEmailAddress())
        {
            //TODO: ASYNC laravel even has hooks to queue up emails natively
            //TODO: refactor this into account
            Mail::send('emails.agrarify.new_account_confirmation', [], function($message) use ($account)
            {
                $message->from(Config::get('agrarify.support_address'), Config::get('agrarify.app_name'));
                $message->to($account->getEmailAddress());
                $message->subject('Please verify your email with ' . Config::get('agrarify.app_name'));
            });
        }

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
