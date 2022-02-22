<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Repository\GuestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @return Response
     */
    public function list(): Response
    {
        $guests = $this->guestRepository->findAll();

        return $guests ?
            $this->json(['guests' => $guests], 200) :
            $this->json(['msg' => 'Empty guest!'], 200);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $guest = new Guest();
        $request = json_decode($request->getContent(), true);

        if (!isset($request['name'], $request['phone'])) {
            return $this->json(['msg' => 'Expected fields: name, phone'], 200);
        }

        $guest->setName($request['name']);
        $this->getDoctrine()->getManager()->persist($guest);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['guest' => $guest], 201);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function show(int $id): Response
    {
        $guest = $this->guestRepository->find($id);

        return $guest ?
            $this->json(['guest' => $guest], 200) :
            $this->json(['msg' => 'Could not find guest'], 404);
    }

    /**
     * @param int $id
     * @param Request $request
     *
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        $guest = $this->guestRepository->find($id);

        if (!isset($guest)) {
            return $this->json(['msg' => 'Could not find guest'], 404);
        }

        $request = json_decode($request->getContent(), true);

        if (!isset($request['name'], $request['phone'])) {
            return $this->json(['msg' => 'Expected fields: name, phone'], 200);
        }

        $guest->setName($request['name']);
        $guest->setPhone($request['phone']);
        $this->getDoctrine()->getManager()->persist($guest);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['guest' => $guest], 200);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function delete(int $id): Response
    {
        $guest = $this->guestRepository->find($id);

        if (!isset($guest)) {
            return $this->json(['msg' => 'Could not find guest'], 404);
        }

        $this->getDoctrine()->getManager()->remove($guest);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['msg' => 'Deleted successfully!'], 200);
    }
}
