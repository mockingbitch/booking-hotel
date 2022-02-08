<?php
namespace App\Service;

use App\Repository\AmountRepository;

class AmountService
{
    /**
     * @var AmountRepository
     */
    private $amountRepository;

    /**
     * @param AmountRepository $amountRepository
     */
    public function __construct(AmountRepository $amountRepository)
    {
        $this->amountRepository = $amountRepository;
    }

    public function setPrice($id)
    {
        $room = $this->amountRepository->findByRoomId($id);
        dd($room);
    }
}