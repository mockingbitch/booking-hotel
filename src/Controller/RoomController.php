<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function list()
    {
        $rooms = $this->roomRepository->findAll();

        return $this->json([
            'rooms'=>$rooms
        ]);
    }

    /**
     * @param Request $request
     *
     * @return false|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create(Request $request)
    {
        $room = new Room();
        $roomRequest = json_decode($request->getContent(), true);
        if ($roomRequest == null)
        {
            return false;
        }
        $room->setName($roomRequest['name']);
        $this->getDoctrine()->getManager()->persist($room);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'room'=>$room
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

        return $this->json([
            'room'=>$room
        ]);
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
        $request = json_decode($request->getContent(),true);
        $room->setName($request['name']);
        $this->getDoctrine()->getManager()->persist($room);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'room'=>$room
        ]);
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        $room = $this->roomRepository->find($id);
        $this->getDoctrine()->getManager()->remove($room);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'room'=>$room
        ]);
    }
}
