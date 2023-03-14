<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Controller;

use App\Repository\OrdersRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

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

    #[Route('/api/spin-the-wheel', name: 'api_spinthewheel', methods: ['POST'])]
    public function setSpinTheWheelWinner(Request $request): Response
    {
        $winner = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR)['winner'] ?? null;
        $cache = new FilesystemAdapter();
        $cache->get('spinthewheel', function (ItemInterface $item) use ($winner) {
            $item->expiresAt(new DateTime('tomorrow 3am'));

            return $winner;
        });

        return new Response();
    }
}
