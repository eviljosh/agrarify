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
     * @param array $access_token
     * @return array
     */
    public function transform($access_token)
    {
        return [
            'id'              => (int) $this->getArrayValueOrDefault($access_token, 'id', -1),
            'name'            => $this->getArrayValueOrDefault($access_token, 'name'),
            'description'     => $this->getArrayValueOrDefault($access_token, 'description'),
            'type'            => $this->getArrayValueOrDefault($access_token, 'type'),
            'consumer_id'     => $this->getArrayValueOrDefault($access_token, 'consumer_id'),
            'consumer_secret' => $this->getArrayValueOrDefault($access_token, 'consumer_secret'),
        ];
    }

}