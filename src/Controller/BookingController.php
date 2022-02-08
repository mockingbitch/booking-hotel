<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\BookingDetail;
use App\Repository\BookingRepository;
use App\Repository\GuestRepository;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param BookingRepository $bookingRepository
     * @param GuestRepository $guestRepository
     * @param RoomRepository $roomRepository
     */
   public function __construct(
       BookingRepository $bookingRepository,
       GuestRepository $guestRepository,
       RoomRepository $roomRepository
   )
   {
       $this->bookingRepository = $bookingRepository;
       $this->guestRepository = $guestRepository;
       $this->roomRepository = $roomRepository;
   }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
   public function list()
   {
       $bookings = $this->bookingRepository->findAll();

       return $this->json([
           'bookings'=>$bookings
       ],200);
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
       $guest = $this->guestRepository->find($request['guest_id']);
       $rooms = $request['room_id']??'';
       $start_date = $request['start_date']??'';
       $end_date = $request['end_date']??'';
       $booking->setGuest($guest);
       $booking->setDate(\DateTime::createFromFormat('Y-m-d',$request['date']));
       $booking->setStatus(0);
       $this->getDoctrine()->getManager()->persist($booking);
       foreach ($rooms as $room)
       {
           $bookingDetails = new BookingDetail();
           $room = $this->roomRepository->findById($room);
           $bookingDetails->setBooking($booking);
           $bookingDetails->setRoom($room);
           $bookingDetails->setStartDate(\DateTime::createFromFormat('Y-m-d',$start_date));
           $bookingDetails->setEndDate(\DateTime::createFromFormat('Y-m-d',$end_date));
           $bookingDetails->setTotal('100000');
           $this->getDoctrine()->getManager()->persist($bookingDetails);
       }
       $this->getDoctrine()->getManager()->flush();

       return $this->json([
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

       return $this->json([
           'booking'=>$booking
       ],200);
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
       $request = json_decode($request->getContent(),true);
       if ($request == null){
           return false;
       }
       $booking->setTotalAmount($request['totalAmount']);
       $booking->setStatus($request['status']);

       return $this->json([
           'booking'=>$booking
       ],201);
   }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
   public function delete($id)
   {
       $booking = $this->bookingRepository->find($id);
       $this->getDoctrine()->getManager()->remove($booking);
       $this->getDoctrine()->getManager()->flush();

       return $this->json([
           'booking'=>$booking
       ],200);
   }
}
