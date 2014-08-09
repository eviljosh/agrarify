<?php

namespace Agrarify\Transformers;

class OauthConsumerTransformer extends AgrarifyTransformer
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->plural_name = 'oauthConsumers';
    }

    /**
     * Transforms a single model record.
     *
     * @param array $consumer
     * @return array
     */
    public function transform($consumer)
    {
        return [
            'id'              => (int) $this->getArrayValueOrDefault($consumer, 'id', -1),
            'name'            => $this->getArrayValueOrDefault($consumer, 'name'),
            'description'     => $this->getArrayValueOrDefault($consumer, 'description'),
            'type'            => $this->getArrayValueOrDefault($consumer, 'type'),
            'consumer_id'     => $this->getArrayValueOrDefault($consumer, 'consumer_id'),
            'consumer_secret' => $this->getArrayValueOrDefault($consumer, 'consumer_secret'),
        ];
    }

}