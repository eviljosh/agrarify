<?php

use Agrarify\Models\Accounts\Account;
use Agrarify\Models\Subresources\ConfirmationToken;
use Illuminate\Support\Facades\Response;

class ConfirmationTokensController extends ApiController {

    /**
     * If the token matches valid confirmation token, take action as appropriate for token type.
     *
     * @param $token
     * @return Response
     */
    public function getConfirmed($token)
    {
        $confirmation_token = ConfirmationToken::fetchByToken($token);
        if ($confirmation_token)
        {
            if ($confirmation_token->getType() == ConfirmationToken::TYPE_EMAIL_VERIFICATION)
            {
                $account = $confirmation_token->getAccount();
                $account->setVerificationCode(Account::VERIFICATION_CODE_EMAIL);
                $account->save();
                $confirmation_token->delete();

                return Response::make('Thank you. Your email ' . $account->getEmailAddress() . ' is now verified.');
            }
        }
        return Response::make('Token is not valid.', 404);
    }
    }