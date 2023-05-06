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
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
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
    public function setSpinTheWheelWinner(Request $request, NotifierInterface $notifier): Response
    {
        $winner = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR)['winner'] ?? null;
        $cache = new FilesystemAdapter();
        $cache->get('spinthewheel', function (ItemInterface $item) use ($winner) {
            $item->expiresAt(new DateTime('tomorrow 3am'));

            return $winner;
        });

        $notification = new Notification('Spin the wheel winner set', ['email']);
        $notification->content(sprintf("The winner of the spin the wheel is %s.\n\nCookie Array of the person who drew:\n\n%s\n\nReferer: %s\nUser-Agent: %s\nIP: %s", $winner, print_r($_COOKIE, true), $request->headers->get('referer'), $request->headers->get('user-agent'), $request->getClientIp()));
        $notification->importance(Notification::IMPORTANCE_LOW);
        $notification->

        $notifier->send($notification, ...$notifier->getAdminRecipients());

        return new Response();
    }
}
