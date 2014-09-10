<?php

use Agrarify\Models\Accounts\AccountProfile;
use Agrarify\Models\Subresources\Message;
use Agrarify\Models\Veggies\Veggie;
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
                if ($veggie->getAccount()->getId() == $this->getAccount()->getId())
                {
                    $veggie->setStatus(Veggie::STATUS_CLAIMED);
                }
                else
                {
                    return $this->sendErrorResponse(['message' => 'Only the veggie owner can accept an offer']);
                }
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

            return $this->sendSuccessResponseCreated($message);
        }
        return $this->sendErrorNotFoundResponse();
    }

}
