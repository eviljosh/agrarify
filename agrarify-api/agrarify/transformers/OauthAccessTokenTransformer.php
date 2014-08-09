<?php

namespace Agrarify\Transformers;

class OauthAccessTokenTransformer extends AgrarifyTransformer
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->plural_name = 'oauth_access_tokens';
    }

    /**
     * Transforms a single model record.
     *
     * @param array $access_token
     * @return array
     */
    public function transform($access_token)
    {
        return [
            //'id'                => (int) $this->getArrayValueOrDefault($access_token, 'id', -1),
            //'account_id'        => (int) $this->getArrayValueOrDefault($access_token, 'account_id', -1),
            //'oauth_consumer_id' => (int) $this->getArrayValueOrDefault($access_token, 'oauth_consumer_id', -1),
            'token'             => $this->getArrayValueOrDefault($access_token, 'token'),
            'token_type'        => 'Bearer',
        ];
    }

}