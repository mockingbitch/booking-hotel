<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Repository\GuestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GuestController extends AbstractController
{
    /**
     * @var GuestRepository
     */
    private $guestRepository;

    /**
     * @param GuestRepository $guestRepository
     */
    public function __construct(GuestRepository $guestRepository)
    {
        $this->guestRepository = $guestRepository;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function list()
    {
        $guests = $this->guestRepository->findAll();

        return $guests?
            $this->json(['guests' => $guests],200):
            $this->json(['msg' => 'Empty guest!'],200);
    }

    /**
     * @param Request $request
     *
     * @return false|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create(Request $request)
    {
        $guest = new Guest();
        $request = json_decode($request->getContent(), true);
        if (!isset($request['name'], $request['phone']))
        {
            return $this->json(['msg' => 'Expected fields: name, phone'],200);
        }
        $guest->setName($request['name']);
        $guest->setPhone($request['phone']);
        $this->getDoctrine()->getManager()->persist($guest);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['guest' => $guest],201);
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function show($id)
    {
        $guest = $this->guestRepository->find($id);

        return $guest?
            $this->json(['guest' => $guest],200):
            $this->json(['msg' => 'Could not find guest'],404);
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update($id, Request $request)
    {
        $guest = $this->guestRepository->find($id);
        if (!isset($guest))
        {
            return $this->json(['msg' => 'Could not find guest'],404);
        }
        $request = json_decode($request->getContent(),true);
        if (!isset($request['name'], $request['phone']))
        {
            return $this->json(['msg' => 'Expected fields: name, phone'],200);
        }
        $guest->setName($request['name']);
        $guest->setPhone($request['phone']);
        $this->getDoctrine()->getManager()->persist($guest);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['guest' => $guest],200);
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        $guest = $this->guestRepository->find($id);
        if (!isset($guest))
        {
            return $this->json(['msg' => 'Could not find guest'],404);
        }
        $this->getDoctrine()->getManager()->remove($guest);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['msg' => 'Deleted successfully!'],200);
    }
}
