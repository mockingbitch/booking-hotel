<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Service\RoomService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class
RoomController extends AbstractController
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

    /**
     * @return false|string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function list()
    {
        $rooms = $this->roomRepository->findAll();

        return $rooms?
            $this->json(['rooms'=>$rooms],200):
            $this->json(['msg'=>'Empty room!'],200);
    }

    /**
     * @param Request $request
     *
     * @return false|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create(Request $request)
    {
        $room = new Room();
        $request = json_decode($request->getContent(), true);
        if (!isset($request['name']))
        {
            return $this->json([
               'msg' => 'Expected field: name'
            ],200);
        }
        $room->setName($request['name']);
        $this->getDoctrine()->getManager()->persist($room);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'room' => $room
        ],201);
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function show($id)
    {
        $room = $this->roomRepository->find($id);

        return $room?
            $this->json(['room' => $room],200):
            $this->json(['msg' => 'Could not find room!'],404);
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update($id, Request $request)
    {
        $room = $this->roomRepository->find($id);
        if (!isset($room))
        {
            return $this->json([
               'msg' => 'Could not find room!'
            ],404);
        }
        $request = json_decode($request->getContent(),true);
        if (!isset($request['name']))
        {
            return $this->json([
                'msg' => 'Expected value Name!'
            ],200);
        }
        $room->setName($request['name']);
        $this->getDoctrine()->getManager()->persist($room);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'room' => $room
        ],200);
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        $room = $this->roomRepository->find($id);
        if (!isset($room))
        {
            return $this->json([
                'msg' => 'Could not find room!'
            ],404);
        }
        $this->getDoctrine()->getManager()->remove($room);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'msg' => 'Delete successfully!'
        ],200);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function search(Request $request)
    {
        $name = $request->query->get('name');
        $date = $request->query->get('date');
        $min = $request->query->get('min');
        $max = $request->query->get('max');
        $start_date = $request->query->get('start_date');
        $end_date = $request->query->get('end_date');
        if ($name !== null)
        {
            $rooms = $this->roomRepository->findByName($name);

            return $rooms?
                $this->json(['rooms' => $rooms],200):
                $this->json(['msg' => 'Could not find room!'],200);
        }
        elseif ($date !== null)
        {
            $rooms = $this->roomRepository->findByDay($date);

            return $rooms?
                $this->json(['rooms' => $rooms],200):
                $this->json(['msg' => 'Could not find room!'],200);
        }
        elseif($min !== null && $max !== null)
        {
            $rooms = $this->roomRepository->findByPriceRange($min, $max);

            return $rooms?
                $this->json(['rooms' => $rooms],200):
                $this->json(['msg' => 'Could not find room!'],200);
        }
        elseif ($start_date !== null && $end_date !== null)
        {
            $rooms = $this->roomRepository->findByDayRange($start_date, $end_date);

            return $rooms?
                $this->json(['rooms' => $rooms],200):
                $this->json(['msg' => 'Could not find room!'],200);
        }

        return $this->json(['msg' => 'Could not find room!', 200]);
    }
}
