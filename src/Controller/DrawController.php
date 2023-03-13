<?php

namespace App\Controller;

use App\Repository\OrdersRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DrawController extends AbstractController
{
    #[Route('/draw', name: 'app_draw')]
    public function index(OrdersRepository $ordersRepository): Response
    {
        $participants = $ordersRepository->createQueryBuilder('o')
            ->andWhere('o.for_date = :date')
            ->setParameter('date', (new DateTime())->format('Y-m-d'))
            ->getQuery()
            ->getResult();
        $participants = array_map(function ($participant) {
            return $participant->getOrderedBy();
        }, $participants);
        return $this->render('draw/index.html.twig', [
            'participants' => $participants,
        ]);
    }
}
