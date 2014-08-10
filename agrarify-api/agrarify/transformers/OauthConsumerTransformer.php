<?php

namespace Agrarify\Transformers;

class OauthConsumerTransformer extends AgrarifyTransformer
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->plural_name = 'oauth_consumers';
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
            'id'              => (int) $this->getArrayValueOrDefault($account, 'id', -1),
            'name'            => $this->getArrayValueOrDefault($account, 'name'),
            'description'     => $this->getArrayValueOrDefault($account, 'description'),
            'type'            => $this->getArrayValueOrDefault($account, 'type'),
            'consumer_id'     => $this->getArrayValueOrDefault($account, 'consumer_id'),
            'consumer_secret' => $this->getArrayValueOrDefault($account, 'consumer_secret'),
        ];
    }

}