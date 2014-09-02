<?php

use Agrarify\Models\Subresources\Availability;
use Agrarify\Models\Subresources\Location;
use Agrarify\Models\Veggies\Veggie;
use Agrarify\Transformers\VeggieTransformer;
use Illuminate\Support\Facades\Response;

// TODO: remove once have search controller
use League\Geotools\Geotools;
use League\Geotools\Coordinate\Coordinate;

class VeggiesController extends ApiController {

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transformer = new VeggieTransformer();
    }

    /**
     * Display a list of available veggies for the app.
     *
     * @return Response
     */
    public function optionsList()
    {
        return Response::json([ 'veggie_options' => [
            1 => ['name' => 'Apples', 'collective_noun' => 'apples'],
            2 => ['name' => 'Asparagus', 'collective_noun' => 'bunches'],
            3 => ['name' => 'Artichokes', 'collective_noun' => 'artichokes'],
            4 => ['name' => 'Avocados', 'collective_noun' => 'avocados'],
            5 => ['name' => 'Broccoli', 'collective_noun' => 'heads'],
            6 => ['name' => 'Beans (Green)', 'collective_noun' => 'handfulls'],
            7 => ['name' => 'Beans (Dry)', 'collective_noun' => 'handfulls'],
            8 => ['name' => 'Bok Choy', 'collective_noun' => 'heads'],
            9 => ['name' => 'Basil', 'collective_noun' => 'bunches'],
            10 => ['name' => 'Beets', 'collective_noun' => 'beets'],
            11 => ['name' => 'Cilantro', 'collective_noun' => 'bunches'],
            12 => ['name' => 'Cabbage', 'collective_noun' => 'heads'],
            13 => ['name' => 'Cucumbers', 'collective_noun' => 'cucumbers'],
            14 => ['name' => 'Carrots', 'collective_noun' => 'bunches'],
            15 => ['name' => 'Cauliflower', 'collective_noun' => 'heads'],
            16 => ['name' => 'Celery', 'collective_noun' => 'heads'],
            17 => ['name' => 'Cherries', 'collective_noun' => 'handfulls'],
            18 => ['name' => 'Eggplant', 'collective_noun' => 'eggplants'],
            19 => ['name' => 'Garlic', 'collective_noun' => 'heads'],
            20 => ['name' => 'Kale', 'collective_noun' => 'heads'],
            21 => ['name' => 'Lettuce', 'collective_noun' => 'heads'],
            22 => ['name' => 'Lemons', 'collective_noun' => 'lemons'],
            23 => ['name' => 'Limes', 'collective_noun' => 'limes'],
            24 => ['name' => 'Oranges', 'collective_noun' => 'oranges'],
            25 => ['name' => 'Radishes', 'collective_noun' => 'bunches'],
            26 => ['name' => 'Spinach', 'collective_noun' => 'bunches'],
            27 => ['name' => 'Chard', 'collective_noun' => 'bunches'],
            28 => ['name' => 'Turnips', 'collective_noun' => 'bunches'],
            29 => ['name' => 'Pumpkins', 'collective_noun' => 'pumpkins'],
            30 => ['name' => 'Squashes', 'collective_noun' => 'squashes'],
            31 => ['name' => 'Peppers (Bell)', 'collective_noun' => 'peppers'],
            32 => ['name' => 'Peppers (Hot)', 'collective_noun' => 'peppers'],
            33 => ['name' => 'Tomatoes', 'collective_noun' => 'tomatoes'],
            34 => ['name' => 'Onions', 'collective_noun' => 'onions'],
            35 => ['name' => 'Leeks', 'collective_noun' => 'bunches'],
            36 => ['name' => 'Plums', 'collective_noun' => 'plums'],
            37 => ['name' => 'Grapes', 'collective_noun' => 'bunches'],
            38 => ['name' => 'Potatoes', 'collective_noun' => 'potatoes'],
            39 => ['name' => 'Potatoes (Sweet)', 'collective_noun' => 'potatoes'],
        ]]);
    }


    /**
     * Display all veggies created by this account in the recent past.
	 *
	 * @return Response
	 */
	public function index()
	{
        return $this->sendSuccessResponse(
            Veggie::fetchByAccountForDaysPast($this->getAccount()),
            [VeggieTransformer::OPTIONS_SHOULD_SEE_DETAILS => true]
        );
	}

    /**
     * Display a veggie by id.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        /**
         * @var $veggie \Agrarify\Models\Veggies\Veggie
         */
        $veggie = Veggie::find($id);

        if ($veggie)
        {
            $options = [];
            if ($veggie->shouldAccountSeeDetails($this->getAccount()))
            {
                $options = [VeggieTransformer::OPTIONS_SHOULD_SEE_DETAILS => true];
            }

            return $this->sendSuccessResponse($veggie, $options);
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Create a veggie associated with this account.
     *
     * @return Response
     */
    public function store()
    {
        $payload = $this->assertRequestPayloadItem();
        $veggie = new Veggie($payload);
        $veggie->setAccount($this->getAccount());

        // Handle the location
        if (isset($payload['location']))
        {
            $location_payload = $payload['location'];

            if (isset($location_payload['id']))
            {
                if ($location = $this->getAccount()->getLocationById($location_payload['id']))
                {
                    $veggie->setLocation($location);
                }
                else
                {
                    return $this->sendErrorResponse(['message' => 'Location with given id does not exist']);
                }
            }
            else
            {
                $location = new Location($location_payload);
                $location->setAccount($this->getAccount());
                $this->assertValid($location);
                $location->save();
                $veggie->setLocation($location);
            }
        }
        else
        {
            return $this->sendErrorResponse(['message' => 'Location is required']);
        }

        // Handle availability
        if (isset($payload['availability']))
        {
            $availability_payload = $payload['availability'];
            $availability = new Availability($availability_payload);
            $this->assertValid($availability);
            $availability->save();
            $veggie->setAvailability($availability);
        }

        // Set defaults
        if (!$veggie->getStatus())
        {
            $veggie->setStatus(Veggie::STATUS_AVAILABLE);
        }
        if (!$veggie->getNotes())
        {
            $veggie->setNotes('Looking for a good home!');
        }

        // Validate and save
        $this->assertValid($veggie);
        $veggie->save();
        return $this->sendSuccessResponseCreated($veggie, [VeggieTransformer::OPTIONS_SHOULD_SEE_DETAILS => true]);
    }

    /**
     * Update a veggie by id.
     *
     * @param $id
     * @return Response
     */
    public function update($id)
    {
        $payload = $this->assertRequestPayloadItem();

        $veggie = $this->getAccount()->getVeggieById($id);
        if ($veggie)
        {
            $veggie->fill($payload);

            // Handle the location
            if (isset($payload['location']))
            {
                $location_payload = $payload['location'];

                if (isset($location_payload['id']))
                {
                    if ($location_payload['id'] != $veggie->getLocation()->getId())
                    {
                        if ($location = $this->getAccount()->getLocationById($location_payload['id']))
                        {
                            $veggie->setLocation($location);
                        }
                        else
                        {
                            return $this->sendErrorResponse(['message' => 'Location with given id does not exist']);
                        }
                    }
                    else
                    {
                        $location = $veggie->getLocation();
                        $location->fill($location_payload);
                        $this->assertValid($location);
                        $location->save();
                    }
                }
                else
                {
                    $location = new Location($location_payload);
                    $location->setAccount($this->getAccount());
                    $this->assertValid($location);
                    $location->save();
                    $veggie->setLocation($location);
                }
            }

            // Handle availability
            if (isset($payload['availability']))
            {
                $availability_payload = $payload['availability'];

                $availability = $veggie->getAvailability();
                if ($availability)
                {
                    $availability->fill($availability_payload);
                }
                else
                {
                    $availability = new Availability($availability_payload);
                }

                $this->assertValid($availability);
                $availability->save();
                $veggie->setAvailability($availability);
            }

            // Validate and save
            $this->assertValid($veggie);
            $veggie->save();
            return $this->sendSuccessResponse($veggie, [VeggieTransformer::OPTIONS_SHOULD_SEE_DETAILS => true]);
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Delete a veggie by id.
     *
     * @param $id
     * @return Response
     */
    public function destroy($id)
    {
        $veggie = $this->getAccount()->getVeggieById($id);
        if ($veggie)
        {
            $veggie->delete();
            return $this->sendSuccessNoContentResponse();
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Temporary "fake" veggie search (pre-ElasticSearch)
     *
     * @return Response
     */
    public function testSearch()
    {
        $lat = Input::get('latitude', '37.77492950');
        $lon = Input::get('longitude', '-122.4194155');
        $type = Input::get('type', '1');

        $coord = new Coordinate($lat . ', ' . $lon);
        $geotool = new Geotools();
        $encoded = $geotool->geohash()->encode($coord);
        $geohash = $encoded->getGeohash();

        $results = [];
        for ($n = 12; $n > 3; $n--)
        {
            $geohash_substring = substr($geohash, 0, $n) . '%';
            $veggies = Veggie::whereHas('location', function($q) use ($geohash_substring)
                {
                    $q->where('geohash', 'like', $geohash_substring);
                })
                ->where('type', '=', $type)
                ->get();

            $current_veggies = [];
            foreach ($veggies as $veggie)
            {
                $veggie_coord = $veggie->getLocation()->getCoordinate();
                $point = $geotool->point()->setFrom($coord)->setTo($veggie_coord);
                $veggie->direction = $point->initialCardinal();

                $distance = $geotool->distance()->setFrom($coord)->setTo($veggie_coord);
                $veggie->distance = $distance->in('mi')->haversine();
            }
            $veggies_array = iterator_to_array($veggies);
            $results = array_merge($results, $veggies_array);

            if (count($results) > 10)
            {
                break;
            }
        }

        return $this->sendSuccessResponse($results);
    }

}
