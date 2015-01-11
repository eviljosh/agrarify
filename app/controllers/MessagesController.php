<?php

use Agrarify\Models\Accounts\AccountProfile;
use Agrarify\Models\Subresources\Message;
use Agrarify\Models\Veggies\Veggie;
use Agrarify\Models\Veggies\VeggieOptions;
use Agrarify\Transformers\MessageTransformer;
use Illuminate\Support\Facades\Response;

class MessagesController extends ApiController {

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transformer = new MessageTransformer($this->getAccount());
    }

	/**
     * Display all recent veggie messages associated with this account.
	 *
	 * @return Response
	 */
	public function listVeggieMessages()
	{
        $messages = Message::fetchVeggieMessagesByAccountForDaysPast($this->getAccount(), 10);
        return $this->sendSuccessResponse($messages);
	}

    /**
     * Display all messages for the given veggie associated with this account.
     *
     * @param $id
     * @return Response
     */
    public function showVeggieMessages($id)
    {
        /**
         * @var \Agrarify\Models\Veggies\Veggie $veggie
         */
        $veggie = Veggie::find($id);

        if ($veggie)
        {
            $messages = Message::fetchMessagesForVeggieAndAccount($this->getAccount(), $veggie);
            return $this->sendSuccessResponse($messages);
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Create a message associated with the given veggie and account.
     *
     * @param mixed $id The veggie id
     * @return Response
     */
    public function createVeggieMessage($id)
    {
        $payload = $this->assertRequestPayloadItem();

        /**
         * @var \Agrarify\Models\Veggies\Veggie $veggie
         */
        $veggie = Veggie::find($id);

        if ($veggie)
        {
            $message = new Message($payload);
            $message->setAccount($this->getAccount());
            $message->setOtherId($veggie->getId());

            // Make sure message type is acceptable
            if ($message->getType() == Message::TYPE_VEGGIE_OFFER_ACCEPTANCE)
            {
                if ($veggie->getStatus() == Veggie::STATUS_CLAIMED)
                {
                    return $this->sendErrorResponse(['message' => 'This veggie has already been claimed.']);
                }

                if ($veggie->getAccount()->getId() == $this->getAccount()->getId())
                {
                    $veggie->setStatus(Veggie::STATUS_CLAIMED);
                }
                else
                {
                    return $this->sendErrorResponse(['message' => 'Only the veggie owner can accept an offer']);
                }
            }
            elseif ($message->getType() == Message::TYPE_VEGGIE_OFFER and $veggie->getStatus() == Veggie::STATUS_CLAIMED)
            {
                return $this->sendErrorResponse(['message' => 'This veggie has already been claimed.']);
            }

            // Handle recipient profile
            if (isset($payload['to_profile']) and isset($payload['to_profile']['profile_slug']))
            {
                $profile = AccountProfile::fetchBySlug($payload['to_profile']['profile_slug']);
                if ($profile)
                {
                    $message->setRecipientAccount($profile->getAccount());
                }
                else
                {
                    return $this->sendErrorResponse(['message' => 'to_profile - No profile found for given slug']);
                }
            }
            else
            {
                return $this->sendErrorResponse(['message' => 'to_profile - Recipient profile slug is required']);
            }

            // Validate and save
            $this->assertValid($message);

            DB::transaction(function () use (&$message, &$veggie) {
                $message->save();
                $veggie->save();
            });

            $veggie_name = VeggieOptions::getVeggieNameForCode($veggie->getType());
            $message_text = '';
            if ($message->getMessage())
            {
                $message_text = $this->getAccount()->getProfile()->getDisplayName() . ': ';
                $message_text .= str_limit($message->getMessage(), $limit = 100, $end = '...');
            }

            $push_title = 'New Message';
            if ($message->getType() == Message::TYPE_VEGGIE_OFFER)
            {
                $push_title = $veggie_name . ' request!';
            }
            elseif ($message->getType() == Message::TYPE_VEGGIE_OFFER_ACCEPTANCE)
            {
                $push_title = 'You got ' . $veggie_name . '!';
            }
            elseif ($message->getType() == Message::TYPE_VEGGIE_OFFER_REJECTED)
            {
                $push_title = $veggie_name . ' request rejected.';
            }
            $message->getRecipientAccount()->sendFormattedPushNotification($push_title, $message_text, $veggie->getId());

            return $this->sendSuccessResponseCreated($message);
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Update the given message
     *
     * @param $id
     * @return Response
     */
    public function update($id)
    {
        $payload = $this->assertRequestPayloadItem();

        $message = Message::fetchMessageForAccount($this->getAccount(), $id);

        if ($message)
        {
            // we don't use fill here because only these two fields are editable at the moment

            if (isset($payload['read_by_recipient']))
            {
                $message->setReadByRecipient($payload['read_by_recipient']);
            }

            if (isset($payload['ignored_by_recipient']))
            {
                $message->setIgnoredByRecipient($payload['ignored_by_recipient']);
            }

            $this->assertValid($message);
            $message->save();

            return $this->sendSuccessResponse($message);
        }
        return $this->sendErrorNotFoundResponse();
    }

}
