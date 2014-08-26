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
            'id'              => (int) $this->getValueOrDefault($consumer, 'id', -1),
            'name'            => $this->getValueOrDefault($consumer, 'name'),
            'description'     => $this->getValueOrDefault($consumer, 'description'),
            'type'            => $this->getValueOrDefault($consumer, 'type'),
            'consumer_id'     => $this->getValueOrDefault($consumer, 'consumer_id'),
            'consumer_secret' => $this->getValueOrDefault($consumer, 'consumer_secret'),
        ];
    }

}