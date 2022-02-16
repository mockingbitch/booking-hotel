<?php

namespace App\Controller;

use App\Entity\Availability;
use App\Entity\Booking;
use App\Entity\BookingDetail;
use App\Repository\AmountRepository;
use App\Repository\AvailabilityRepository;
use App\Repository\BookingRepository;
use App\Repository\GuestRepository;
use App\Repository\RoomRepository;
use App\Service\AvailabilityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Response;

class BookingController extends AbstractController
{
    /**
     * @var BookingRepository
     */
   private $bookingRepository;

    /**
     * @var $roomRepository
     */
   private $roomRepository;

    /**
     * @var GuestRepository
     */
   private $guestRepository;

    /**
     * @var $amountRepository
     */
   private $amountRepository;

    /**
     * @var $availabilityService
     */
   private $availabilityService;

    /**
     * @var $availabilityRepository
     */
   private $availabilityRepository;

    /**
     * @param BookingRepository $bookingRepository
     * @param GuestRepository $guestRepository
     * @param RoomRepository $roomRepository
     * @param AmountRepository $amountRepository
     * @param AvailabilityService $availabilityService
     */
   public function __construct(
       BookingRepository $bookingRepository,
       GuestRepository $guestRepository,
       RoomRepository $roomRepository,
       AmountRepository $amountRepository,
       AvailabilityService $availabilityService,
       AvailabilityRepository $availabilityRepository)
   {
       $this->bookingRepository = $bookingRepository;
       $this->guestRepository = $guestRepository;
       $this->roomRepository = $roomRepository;
       $this->amountRepository = $amountRepository;
       $this->availabilityService = $availabilityService;
       $this->availabilityRepository = $availabilityRepository;
   }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
   public function list()
   {
       $bookings = $this->bookingRepository->findAll();

       return $bookings?
           $this->json(['bookings' => $bookings],200):
           $this->json(['msg' => 'Empty booking'],200);
   }

    /**
     * @param Request $request
     *
     * @return false|\Symfony\Component\HttpFoundation\JsonResponse
     */
   public function create(Request $request)
   {
       $booking = new Booking();
       $request = json_decode($request->getContent(), true);
       if (!isset($request['rooms'],$request['start_date'],$request['end_date'],$request['guest']))
       {
            return $this->json(['msg' => 'Expected fields: room, guest, start_date, end_date'],200);
       }
       $dates = $this->amountRepository->dateRange($request['start_date'], $request['end_date']);
       $guest = $this->guestRepository->find($request['guest']);
       $booking->setGuest($guest);
       $booking->setDate(\DateTime::createFromFormat('Y-m-d', date("Y-m-d")));
       $booking->setStatus(0);
       $this->getDoctrine()->getManager()->persist($booking);
       foreach ($request['rooms'] as $room)
       {
           $bookingDetails = new BookingDetail();
           $room = $this->roomRepository->find($room);
           $total = 0;
           foreach ($dates as $date){
               $availabilities = $this->availabilityRepository->findRoom($room, $date);
               foreach ($availabilities as $availability)
               {
                   $stock = $availability->getStock();
                   if ($stock < 1)
                   {
                       return $this->json(['msg' => 'Out of Stock'],200);
                   }
                   $availability->setStock($stock - 1);
                   $this->getDoctrine()->getManager()->persist($availability);
               }
               $amount = $this->amountRepository->findPriceByDay($room,$date)->getPrice();
               $total = $total + $amount;
           }
           $bookingDetails->setBooking($booking);
           $bookingDetails->setRoom($room);
           $bookingDetails->setStartDate(\DateTime::createFromFormat('Y-m-d', $request['start_date']));
           $bookingDetails->setEndDate(\DateTime::createFromFormat('Y-m-d', $request['end_date']));
           $bookingDetails->setTotal($total);
           $this->getDoctrine()->getManager()->persist($bookingDetails);
       }
       $this->getDoctrine()->getManager()->flush();

       return $this->json([
           'booking' => $booking,
           'bookingDetails' => $bookingDetails,
           'availability' => $availability
       ],201);
   }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
   public function show($id)
   {
       $booking = $this->bookingRepository->find($id);

       return $booking?
           $this->json(['booking'=>$booking],200):
           $this->json(['msg'=>'Could not find booking!'],404);
   }

    /**
     * @param $id
     * @param Request $request
     *
     * @return false|\Symfony\Component\HttpFoundation\JsonResponse
     */
   public function update($id, Request $request)
   {
       $booking = $this->bookingRepository->find($id);
       if (!isset($booking))
       {
           return $this->json(['msg' => 'Could not find booking'],404);
       }
       $request = json_decode($request->getContent(),true);
       if (!isset($request['status']))
       {
           return $this->json(['msg' => 'Expected field: status'],200);
       }
       $booking->setStatus($request['status']);
       $this->getDoctrine()->getManager()->persist($booking);
       $this->getDoctrine()->getManager()->flush();

       return $this->json(['booking' => $booking],200);
   }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
   public function delete($id)
   {
       $booking = $this->bookingRepository->find($id);
       if (!isset($booking))
       {
           return $this->json(['msg' => 'Could not find booking!'],404);
       }
       $this->getDoctrine()->getManager()->remove($booking);
       $this->getDoctrine()->getManager()->flush();

       return $this->json(['msg' => 'Deleted successfully!'],200);
   }
}
