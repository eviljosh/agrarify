<?php

namespace Agrarify\Transformers;

class MessageTransformer extends AgrarifyTransformer
{
    /**
     * Constructor
     *
     * @param \Agrarify\Models\Accounts\Account $account
     */
    public function __construct($account)
    {
        $this->account = $account;

        $this->account_profile_transformer = new AccountProfileTransformer();
    }

    /**
     * Transforms a single model record.
     *
     * @param \Agrarify\Models\Subresources\Message $message
     * @param array $options
     * @return array
     */
    public function transform($message, $options = [])
    {
        $json_array = [
            'id' => $message->getId(),
            'type' => $message->getType(),
            'from_me' => $this->account->getId() == $message->getAccount()->getId(),
            'from_profile' => $this->account_profile_transformer->transform(
                    $message->getAccount()->getProfile(),
                    [AccountProfileTransformer::OPTIONS_SHOW_SHORT_PROFILE => true]
                ),
            'to_profile' => $this->account_profile_transformer->transform(
                    $message->getRecipientAccount()->getProfile(),
                    [AccountProfileTransformer::OPTIONS_SHOW_SHORT_PROFILE => true]
                ),
            'message' => $message->getMessage(),
            'sent_at' => $message->getCreatedAt()->toDateTimeString(),
        ];

        if ($message->isVeggieMessage())
        {
            $json_array = array_merge($json_array, [
                'veggie_id' => $message->getOtherId()
            ]);
        }

        return $json_array;
    }

}