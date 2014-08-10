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
     * @param array $account
     * @return array
     */
    public function transform($account)
    {
        return [
            'given_name'        => $this->getArrayValueOrDefault($account, 'given_name'),
            'surname'           => $this->getArrayValueOrDefault($account, 'surname'),
            'email_address'     => $this->getArrayValueOrDefault($account, 'email_address'),
            'verification_code' => $this->getArrayValueOrDefault($account, 'verification_code'),
        ];
    }

}