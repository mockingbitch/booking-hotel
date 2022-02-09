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
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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
               'msg'=>'Expected value Name'
            ],200);
        }
        $room->setName($request['name']);
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

        return $room?
            $this->json(['room'=>$room],200):
            $this->json(['msg'=>'Could not find room!'],404);
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
               'msg'=>'Could not find room!'
            ],404);
        }
        $request = json_decode($request->getContent(),true);
        if (!isset($request['name']))
        {
            return $this->json([
                'msg'=>'Expected value Name!'
            ],200);
        }
        $room->setName($request['name']);
        $this->getDoctrine()->getManager()->persist($room);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'room'=>$room
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
                'msg'=>'Could not find room!'
            ],404);
        }
        $this->getDoctrine()->getManager()->remove($room);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'msg'=>'Delete successfully!'
        ],200);
    }
}
