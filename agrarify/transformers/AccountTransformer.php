<?php

namespace Agrarify\Transformers;

class AccountTransformer extends AgrarifyTransformer
{

    /**
     * Transforms a single model record.
     *
     * @param \Agrarify\Models\Accounts\Account $account
     * @param array $options
     * @return array
     */
    public function transform($account, $options = [])
    {
        return [
            'given_name'        => $account->getGivenName(),
            'surname'           => $account->getSurname(),
            'email_address'     => $account->getEmailAddress(),
            'verification_code' => $account->getVerificationCode(),
        ];
    }

}