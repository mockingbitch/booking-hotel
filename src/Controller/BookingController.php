<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\BookingDetail;
use App\Repository\AmountRepository;
use App\Repository\BookingRepository;
use App\Repository\GuestRepository;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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
     * @param BookingRepository $bookingRepository
     * @param GuestRepository $guestRepository
     * @param RoomRepository $roomRepository
     * @param AmountRepository $amountRepository
     */
   public function __construct(
       BookingRepository $bookingRepository,
       GuestRepository $guestRepository,
       RoomRepository $roomRepository,
       AmountRepository $amountRepository
   )
   {
       $this->bookingRepository = $bookingRepository;
       $this->guestRepository = $guestRepository;
       $this->roomRepository = $roomRepository;
       $this->amountRepository = $amountRepository;
   }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
   public function list()
   {
       $bookings = $this->bookingRepository->findAll();

       return $bookings?
           $this->json(['bookings'=>$bookings],200):
           $this->json(['msg'=>'Empty booking'],200);
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
       if (!isset($request['room'],$request['start_date'],$request['end_date'],$request['guest']))
       {
            return $this->json(['msg'=>'Expected fields: room, guest, start_date, end_date'],200);
       }
       $guest = $this->guestRepository->find($request['guest_id']);
       $booking->setGuest($guest);
       $booking->setDate(\DateTime::createFromFormat('Y-m-d',$request['date']));
       $booking->setStatus(0);
       $this->getDoctrine()->getManager()->persist($booking);
       foreach ($request['rooms'] as $room)
       {
           $bookingDetails = new BookingDetail();
           $room = $this->roomRepository->findById($room);
           $dates = $this->amountRepository->dateRange($request['start_date'],$request['end_date']);
           $total = 0;
           foreach ($dates as $date){
               $amount = $this->amountRepository->findPriceByDay($room,$date)->getPrice();
               $total = $total + $amount;
           }
           dd($total);
           $bookingDetails->setBooking($booking);
           $bookingDetails->setRoom($room);
           $bookingDetails->setStartDate(\DateTime::createFromFormat('Y-m-d',$start_date));
           $bookingDetails->setEndDate(\DateTime::createFromFormat('Y-m-d',$end_date));
           $bookingDetails->setTotal('100000');
           $this->getDoctrine()->getManager()->persist($bookingDetails);
       }
       $this->getDoctrine()->getManager()->flush();

       return $this->json([
           'booking'=>$booking,
           'bookingDetails'=>$bookingDetails
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
           return $this->json(['msg'=>'Could not find booking'],404);
       }
       $request = json_decode($request->getContent(),true);
       if (!isset($request['status']))
       {
           return $this->json(['msg'=>'Expected field: status'],200);
       }
       $booking->setStatus($request['status']);
       $this->getDoctrine()->getManager()->persist($booking);
       $this->getDoctrine()->getManager()->flush();

       return $this->json(['booking'=>$booking],200);
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
           return $this->json(['msg'=>'Could not find booking!'],404);
       }
       $this->getDoctrine()->getManager()->remove($booking);
       $this->getDoctrine()->getManager()->flush();

       return $this->json(['msg'=>'Deleted successfully!'],200);
   }
}
