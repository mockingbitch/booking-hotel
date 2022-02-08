<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Repository\GuestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class GuestController extends ApiController
{
    private $guestRepository;
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

        return $this->json([
            'guests' => $guests
        ]);
    }

    /**
     * @param Request $request
     *
     * @return false|Response
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function create(Request $request)
    {
        $guest = new Guest();
        $request = json_decode($request->getContent(), true);
        if ($request == null)
        {
            return false;
        }
        $guest->setName($request['name']);
        $guest->setPhone($request['phone']);
        $this->getDoctrine()->getManager()->persist($guest);
        $this->getDoctrine()->getManager()->flush();
//        $response = $this->createApiResponse(['guest' => $guest], 201);

        return $this->json([
            'guest'=>$guest
        ]);
    }

    /**
     * @param $id
     *
     * @return Response
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function show($id)
    {
        $guest = $this->guestRepository->find($id);
        $response = $this->createApiResponse(['guest'=>$guest],200);

        return $response;
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function update($id, Request $request)
    {
        $guest = $this->guestRepository->find($id);
        $request = json_decode($request->getContent(),true);
        $guest->setName($request['name']);
        $guest->setPhone($request['phone']);
        $this->getDoctrine()->getManager()->persist($guest);
        $this->getDoctrine()->getManager()->flush();
        $response = $this->createApiResponse(['guest'=>$guest],201);

        return $response;
    }

    /**
     * @param $id
     *
     * @return Response
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function delete($id)
    {
        $guest = $this->guestRepository->find($id);
        $this->getDoctrine()->getManager()->remove($guest);
        $this->getDoctrine()->getManager()->flush();
        $response = $this->createApiResponse('',200);

        return $response;
    }
}
