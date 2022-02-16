<?php

namespace App\Service;

use App\Entity\Availability;
use App\Repository\AvailabilityRepository;
use App\Repository\RoomRepository;

class AvailabilityService
{
    /**
     * @var AvailabilityRepository
     */
    private $availabilityRepository;

    /**
     * @param AvailabilityRepository $availabilityRepository
     */
    public function __construct(AvailabilityRepository $availabilityRepository)
    {
        $this->availabilityRepository = $availabilityRepository;
    }

    /**
     * @param $room
     * @param $date
     *
     * @return bool
     */
    public function checkAvailability($room, $date)
    {
        $availabilities = $this->availabilityRepository->findRoom($room, $date);
        foreach ($availabilities as $availability)
        {
            if ($availability->getStock()>0)
            {
                return true;
            }
        }
        return false;
    }
}