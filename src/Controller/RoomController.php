<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Service\RoomService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends AbstractController
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
     * @return Response
     */
    public function list(): Response
    {
        $rooms = $this->roomRepository->findAll();

        return $rooms ?
            $this->json(['rooms'=>$rooms], 200) :
            $this->json(['msg'=>'Empty room!'], 200);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $room = new Room();
        $request = json_decode($request->getContent(), true);
        if (!isset($request['name'])) {
            return $this->json([
               'msg' => 'Expected field: name'
            ], 200);
        }
        $room->setName($request['name']);
        $this->getDoctrine()->getManager()->persist($room);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'room' => $room
        ], 201);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function show(int $id): Response
    {
        $room = $this->roomRepository->find($id);

        return $room ?
            $this->json(['room' => $room], 200) :
            $this->json(['msg' => 'Could not find room!'], 404);
    }

    /**
     * @param int $id
     * @param Request $request
     *
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        $room = $this->roomRepository->find($id);

        if (!isset($room)) {
            return $this->json([
               'msg' => 'Could not find room!'
            ], 404);
        }

        $request = json_decode($request->getContent(), true);

        if (!isset($request['name'])) {
            return $this->json([
                'msg' => 'Expected value Name!'
            ], 200);
        }

        $room->setName($request['name']);
        $this->getDoctrine()->getManager()->persist($room);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'room' => $room
        ], 200);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function delete(int $id): Response
    {
        $room = $this->roomRepository->find($id);

        if (!isset($room)) {
            return $this->json([
                'msg' => 'Could not find room!'
            ], 404);
        }

        $this->getDoctrine()->getManager()->remove($room);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'msg' => 'Delete successfully!'
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function search(Request $request): Response
    {
        $name = $request->query->get('name');
        $min = $request->query->get('min');
        $max = $request->query->get('max');
        $start_date = $request->query->get('start_date');
        $end_date = $request->query->get('end_date');
        $rooms = $this->roomRepository->findByFields($name, $min, $max, $start_date, $end_date);

        return $rooms ?
            $this->json(['rooms' => $rooms], 200) :
            $this->json(['msg' => 'Could not find room!'], 200);
    }
}
