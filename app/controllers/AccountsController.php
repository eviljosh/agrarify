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
            if ((isset($payload['existing_password']) and $account->isPasswordValid($payload['existing_password']))
                or !$account->hasPassword())
            {
                $account->hashAndSetPassword($payload['password']);
            }
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
            $account->deleteOutstandingConfirmationTokensOfType(\Agrarify\Models\Subresources\ConfirmationToken::TYPE_EMAIL_VERIFICATION);
            $token = new \Agrarify\Models\Subresources\ConfirmationToken();
            $token->setType(\Agrarify\Models\Subresources\ConfirmationToken::TYPE_EMAIL_VERIFICATION);
            $token->setAccount($account);
            $token->save();
            Mail::send('emails.agrarify.new_account_confirmation', ['token' => $token], function($message) use ($account)
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

    /**
     * HTTP POST to forgotten_password
     * Allows users to reset their password
     *
     * @return Response
     */
    public function forgottenPassword()
    {
        $payload = $this->assertRequestPayloadItem();
        $account = Account::fetchByEmail($payload['email_address']);
        if ($account)
        {
            $new_password = str_random(8);
            $account->hashAndSetPassword($new_password);
            $account->save();

            Mail::send('emails.agrarify.password_reset', ['password' => $new_password], function($message) use ($account)
            {
                $message->from(Config::get('agrarify.support_address'), Config::get('agrarify.app_name'));
                $message->to($account->getEmailAddress());
                $message->subject('Your ' . Config::get('agrarify.app_name') . ' password has been reset');
            });

            return Response::make('');
        }
        return $this->sendErrorNotFoundResponse();
    }

}
