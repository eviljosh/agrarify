<?php

namespace Agrarify\Transformers;

class AccountTransformer extends AgrarifyTransformer
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->plural_name = 'accounts';
    }

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
            'given_name'        => $this->getValueOrDefault($account, 'given_name'),
            'surname'           => $this->getValueOrDefault($account, 'surname'),
            'email_address'     => $this->getValueOrDefault($account, 'email_address'),
            'verification_code' => $this->getValueOrDefault($account, 'verification_code'),
        ];
    }

}