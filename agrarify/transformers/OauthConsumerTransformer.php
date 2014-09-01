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
     * @param \Agrarify\Models\Oauth2\OauthConsumer $consumer
     * @param array $options
     * @return array
     */
    public function transform($consumer, $options = [])
    {
        return [
            'id'              => $consumer->getId(),
            'name'            => $consumer->getName(),
            'description'     => $consumer->getDescription(),
            'type'            => $consumer->getType(),
            'consumer_id'     => $consumer->getConsumerId(),
            'consumer_secret' => $consumer->getConsumerSecret(),
        ];
    }

}