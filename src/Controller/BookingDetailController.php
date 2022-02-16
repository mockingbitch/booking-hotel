<?php

namespace App\Controller;

use App\Repository\BookingDetailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BookingDetailController extends AbstractController
{
    /**
     * @var BookingDetailRepository
     */
    private $bookingDetailRepository;

    /**
     * @param BookingDetailRepository $bookingDetailRepository
     */
    public function __construct(BookingDetailRepository $bookingDetailRepository)
    {
        $this->bookingDetailRepository = $bookingDetailRepository;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function list()
    {
        $bookingDetails = $this->bookingDetailRepository->findAll();

        return $bookingDetails?
            $this->json(['bookingDetails' => $bookingDetails],200):
            $this->json(['msg' => 'Empty booking detail!']);
    }
}
