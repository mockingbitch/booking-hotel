<?php

namespace App\Controller;

use App\Entity\Availability;
use App\Repository\AvailabilityRepository;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AvailabilityController extends AbstractController
{
    /**
     * @var AvailabilityRepository
     */
    private $availabilityRepository;

    /**
     * @var RoomRepository
     */
    private $roomRepository;

    /**
     * @param AvailabilityRepository $availabilityRepository
     */
    public function __construct(
        AvailabilityRepository $availabilityRepository,
        RoomRepository $roomRepository)
    {
        $this->availabilityRepository = $availabilityRepository;
        $this->roomRepository = $roomRepository;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function list()
    {
        $availabilities = $this->availabilityRepository->findAll();

        return $availabilities?
            $this->json(['availabilites'=>$availabilities],200):
            $this->json(['msg'=>'Empty availability'],200);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create(Request $request)
    {
        $request = json_decode($request->getContent(),true);
        if (!isset($request['start_date'],$request['end_date'],$request['room'],$request['stock']))
        {
            return $this->json(['msg'=>'Expected fields: room, stock, start_date, end_date'],200);
        }
        $room = $this->roomRepository->find($request['room']);
        if (!isset($room))
        {
            return $this->json(['msg'=>'Could not find room!'],404);
        }
        $dates = $this->availabilityRepository->dateRange($request['start_date'],$request['end_date']);
        foreach($dates as $date)
        {
            $availability = new Availability();
            $availability->setRoom($room);
            $availability->setDay(\DateTime::createFromFormat('Y-m-d',$date));
            $availability->setStock($request['stock']);
            $this->getDoctrine()->getManager()->persist($availability);
        }
        if (isset($request['special_date']))
        {
            $availability = new Availability();
            $availability->setRoom($room);
            $availability->setDay(\DateTime::createFromFormat('Y-m-d',$request['special_date']));
            $availability->setStock($request['stock']);
            $this->getDoctrine()->getManager()->persist($availability);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['availability'=>$availability],201);
    }
}
