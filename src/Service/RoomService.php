<?php
namespace App\Service;

use App\Repository\RoomRepository;

class RoomService
{
    /**
     * @var RoomRepository
     */
    private $roomRepository;

    /**
     * @param RoomRepository $roomRepository
     */
    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function setPrice($id)
    {

    }
}