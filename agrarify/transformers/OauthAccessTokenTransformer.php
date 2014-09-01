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
     * @param \Agrarify\Models\Oauth2\OauthAccessToken $token
     * @param array $options
     * @return array
     */
    public function transform($token, $options = [])
    {
        return [
            //'id'                => (int) $this->getValueOrDefault($access_token, 'id', -1),
            //'account_id'        => (int) $this->getValueOrDefault($access_token, 'account_id', -1),
            //'oauth_consumer_id' => (int) $this->getValueOrDefault($access_token, 'oauth_consumer_id', -1),
            'token'             => $token->getToken(),
            'token_type'        => 'Bearer',
        ];
    }

}