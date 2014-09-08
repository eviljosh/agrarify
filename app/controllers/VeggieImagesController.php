<?php

use Agrarify\Models\Veggies\VeggieImage;
use Agrarify\Transformers\VeggieImageTransformer;
use Illuminate\Support\Facades\Response;

class VeggieImagesController extends ApiController {

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transformer = new VeggieImageTransformer();
    }

    /**
     * Create an image associated with the given veggie.
     *
     * @param $veggie_id
     * @return Response
     */
    public function create($veggie_id)
    {
        if (Input::hasFile('image') and Input::file('image')->isValid())
        {
            $image_file = Input::file('image');

            $veggie = $this->getAccount()->getVeggieById($veggie_id);
            if ($veggie)
            {
                $image = new VeggieImage();
                $image->setVeggie($veggie);
                $image->setImageFile($image_file);
                $image->save();
                return $this->sendSuccessResponseCreated($image);
            }
            return $this->sendErrorNotFoundResponse();
        }
        return $this->sendErrorBadRequestResponse(['message' => 'Could not find valid "image" file in request.']);
    }

    /**
     * Update an image associated with the given veggie.
     *
     * @param $veggie_id
     * @param $image_id
     * @return Response
     */
    public function update($veggie_id, $image_id)
    {
        $payload = $this->assertRequestPayloadItem();

        $veggie = $this->getAccount()->getVeggieById($veggie_id);
        if ($veggie)
        {
            $image = $veggie->getImageById($image_id);
            if ($image)
            {
                $image->fill($payload);
                $image->save();
                return $this->sendSuccessResponse($image);
            }
        }
        return $this->sendErrorNotFoundResponse();
    }

}
