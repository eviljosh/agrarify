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
    public function listOptions()
    {
        $options = [
            [1, 'Apples', 'apples'],
            [2, 'Asparagus', 'bunches'],
            [3, 'Artichokes', 'artichokes'],
            [4, 'Avocados', 'avocados'],
            [5, 'Broccoli', 'heads'],
            [6,  'Beans (Green)', 'handfulls'],
            [7, 'Beans (Dry)', 'handfulls'],
            [8, 'Bok Choy', 'heads'],
            [9, 'Basil', 'bunches'],
            [10, 'Beets', 'beets'],
            [11, 'Cilantro', 'bunches'],
            [12, 'Cabbage', 'heads'],
            [13, 'Cucumbers', 'cucumbers'],
            [14, 'Carrots', 'bunches'],
            [15, 'Cauliflower', 'heads'],
            [16, 'Celery', 'heads'],
            [17, 'Cherries', 'handfulls'],
            [18, 'Eggplant', 'eggplants'],
            [19, 'Garlic', 'heads'],
            [20, 'Kale', 'heads'],
            [21, 'Lettuce', 'heads'],
            [22, 'Lemons', 'lemons'],
            [23, 'Limes', 'limes'],
            [24, 'Oranges', 'oranges'],
            [25, 'Radishes', 'bunches'],
            [26, 'Spinach', 'bunches'],
            [27, 'Chard', 'bunches'],
            [28, 'Turnips', 'bunches'],
            [29, 'Pumpkins', 'pumpkins'],
            [30, 'Squashes', 'squashes'],
            [31, 'Peppers (Bell)', 'peppers'],
            [32, 'Peppers (Hot)', 'peppers'],
            [33, 'Tomatoes', 'tomatoes'],
            [34, 'Onions', 'onions'],
            [35, 'Leeks', 'bunches'],
            [36, 'Plums', 'plums'],
            [37, 'Grapes', 'bunches'],
            [38, 'Potatoes', 'potatoes'],
            [39, 'Potatoes (Sweet)', 'potatoes'],
        ];

        $veggie_options = [];
        foreach ($options as $option)
        {
            $veggie_options[] = [
                'id' => $option[0],
                'name' => $option[1],
                'collective_noun' => $option[2],
            ];
        }

        return Response::json(['items' => $veggie_options]);
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
