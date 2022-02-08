<?php

namespace App\Controller;

use App\Entity\Amount;
use App\Repository\AmountRepository;
use App\Repository\RoomRepository;
use App\Service\AmountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AmountController extends AbstractController
{
    /**
     * @var AmountService
     */
    private $amountService;

    /**
     * @var AmountRepository
     */
    private $amountRepository;

    /**
     * @var RoomRepository
     */
    private $roomRepository;

    /**
     * @param AmountService $amountService
     * @param AmountRepository $amountRepository
     * @param RoomRepository $roomRepository
     */
    public function __construct(
        AmountService $amountService,
        AmountRepository $amountRepository,
        RoomRepository $roomRepository
    )
    {
        $this->amountService = $amountService;
        $this->amountRepository = $amountRepository;
        $this->roomRepository = $roomRepository;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function list()
    {
        $room = $this->amountRepository->findAll();

        return $this->json([
            'room'=>$room
        ],200);
    }

    /**
     * @param $room_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create($room_id,Request $request)
    {
        $room = $this->roomRepository->find($room_id);
        $request = json_decode($request->getContent(),true);
        $start_date = $request['start_date'] ?? '';
        $end_date = $request['end_date']??'';
        $special_date = $request['special_date']??'';
        $date = $this->amountRepository->dateRange($start_date,$end_date);
        foreach($date as $date)
        {
            $amount = new Amount();
            $amount->setRoom($room);
            $amount->setDay(\DateTime::createFromFormat('Y-m-d',$date));
            $amount->setPrice($request['price']);
            $this->getDoctrine()->getManager()->persist($amount);

        }
        if (isset($special_date))
        {
            $amount = new Amount();
            $amount->setRoom($room);
            $amount->setDay(\DateTime::createFromFormat('Y-m-d',$special_date));
            $amount->setPrice($request['price']);
            $this->getDoctrine()->getManager()->persist($amount);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->json([],201);
    }

    /**
     * @param $room_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update($room_id,Request $request)
    {
        $request = json_decode($request->getContent(),true);
        $start_date = $request['start_date'] ?? '';
        $end_date = $request['end_date']??'';
        $special_date = $request['special_date']??'';
        $date = $this->amountRepository->dateRange($start_date,$end_date);
        if (isset($special_date))
        {
            $amount = $this->amountRepository->findByDay($room_id,$special_date);
            $amount->setDay(\DateTime::createFromFormat('Y-m-d',$special_date));
            $amount->setPrice($request['price']);
            $this->getDoctrine()->getManager()->persist($amount);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->json([],201);
    }
}
