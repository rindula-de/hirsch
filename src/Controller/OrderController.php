<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Payhistory;
use App\Form\OrderType;
use App\Repository\HirschRepository;
use App\Repository\OrdersRepository;
use App\Repository\PayhistoryRepository;
use App\Repository\PaypalmesRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrderController extends AbstractController
{
    #[Route('/order/{preorder}/{slug}', name: 'order', methods: ['GET', 'POST'])]
    public function index(
        int $preorder,
        string $slug,
        HirschRepository $hirschRepository,
        PayhistoryRepository $payhistoryRepository,
        PaypalmesRepository $paypalmesRepository,
        Request $request,
        ManagerRegistry $doctrine,
        MessageBusInterface $bus,
        TranslatorInterface $translator
    ): Response {
        $order = new Orders();
        $hirsch = $hirschRepository->findOneBy(['slug' => $slug]);

        if (null === $hirsch) {
            return $this->redirectToRoute('menu');
        }

        $preorder_time = (new DateTime("+$preorder day"))
            ->setTimezone(new \DateTimeZone('Europe/Berlin'))
            ->setTime(0, 0);
        $order->setCreated((new DateTime())
            ->setTimezone(new \DateTimeZone('Europe/Berlin')))
            ->setForDate($preorder_time)
            ->setHirsch($hirsch);

        if ($request->cookies->get('ordererName') && null === $order->getOrderedby()) {
            $order->setOrderedby($request->cookies->get('ordererName', ''));
        }
        $form = $this->createForm(OrderType::class, $order, [
                'for_date' => $order->getForDate()?->format('d.m.Y') ?? (new \DateTime('now'))->format('d.m.Y'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData();
            $em = $doctrine->getManager();
            $response = new RedirectResponse($this->generateUrl('paynow'));

            if ($order instanceof Orders) {
                if (DateTime::createFromFormat('U', time().'') > DateTime::createFromFormat('H:i', '10:56') && 0 === $preorder) {
                    $activePayer = $this->getActivePayer($paypalmesRepository, $payhistoryRepository, $translator);

                    $this->addFlash('error', $translator->trans('order.search_alternative', ['%orderer%' => $activePayer]));

                    return new RedirectResponse($this->generateUrl('menu'));
                }
                $em->persist($order);
                $em->flush();

                // Set ordererName Cookie
                $cookie = new Cookie(
                    'ordererName',
                    $order->getOrderedby(),
                    (new DateTime('+1 year'))->setTimezone(new \DateTimeZone('Europe/Berlin'))
                );

                // create response with cookie
                $response->headers->setCookie($cookie);

                $this->addFlash('raw', sprintf("<script>window.order_id = '%s'</script>", $order->getId()));
            }

            return $response;
        }

        // if its after 10:55 redirect back to menu
        if (0 === $preorder && DateTime::createFromFormat('U', time().'') > DateTime::createFromFormat('H:i', '10:55')) {
            $activePayer = $this->getActivePayer($paypalmesRepository, $payhistoryRepository, $translator);

            $this->addFlash(
                'warning',
                $translator->trans('order.search_alternative', ['%orderer%' => $activePayer])
            );

            return $this->redirectToRoute('menu');
        }

        $this->addFlash('raw', sprintf("<script>window.gericht = '%s'</script>", $hirsch->getName()));

        return $this->renderForm('order/index.html.twig', [
            'form' => $form,
            'meal' => $hirsch,
            'order_date' => $preorder_time,
        ], new Response(null, $form->isSubmitted() ? 422 : 200));
    }

    #[Route('/order-until', name: 'order-until', methods: ['GET'])]
    public function orderuntil(TranslatorInterface $translator): Response
    {
        return new Response($translator->trans('order.until'), 200);
    }

    #[Route('/orders/delete/{id}', name: 'order_delete', methods: ['GET', 'DELETE'])]
    public function delete(Orders $order, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if (DateTime::createFromFormat('U', time().'') >= DateTime::createFromFormat('H:i', '11:00')) {
            $this->addFlash('error', $translator->trans('order.delete.failedLate'));

            return $this->redirectToRoute('menu');
        }
        $entityManager->remove($order);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('order.delete.success'));

        return $this->redirectToRoute('orders');
    }

    /**
     * Get a list of all orders today.
     *
     * @OA\Parameter(
     *     name="onlyToday",
     *     in="path",
     *     description="Nur Bestellungen für heute anzeigen = 1; Alle Bestellungen anzeigen = 0",
     *
     *     @OA\Schema(type="integer")
     * )
     */
    #[Route('/api/orders/{onlyToday?1}', name: 'api_orders', methods: ['GET'])]
    public function api_orders(Request $request, OrdersRepository $ordersRepository, bool $onlyToday = true): Response
    {
        $orders = $ordersRepository->findAll();
        $data = [];
        foreach ($orders as $order) {
            if (!$onlyToday || ($order->getForDate() && $order->getForDate()->format('Y-m-d') === (new DateTime())->format('Y-m-d'))) {
                $data[] = [
                    'id' => $order->getId(),
                    'orderedby' => $order->getOrderedby(),
                    'created' => $order->getCreated(),
                    'forDate' => $order->getForDate(),
                    'note' => $order->getNote(),
                    'ordered' => $order->getHirsch()?->getName(),
                    'orderedSlug' => $order->getHirsch()?->getSlug(),
                ];
            }
        }

        $frameId = $request->headers->get('Turbo-Frame');
        if (null === $frameId) {
            return new JsonResponse($data);
        }
        $orders = [];
        foreach ($data as $d) {
            $orders[$d['ordered']][$d['note']] = ($orders[$d['ordered']][$d['note']] ?? 0) + 1;
        }
        $rows = 1;
        foreach ($orders as $order => $notes) {
            foreach ($notes as $note => $amount) {
                /* @var string $note */
                ++$rows;

                if (strlen($note) > 0) {
                    ++$rows;
                }
            }
        }

        if ('orders_area' == $frameId) {
            return $this->render('order/orders_textarea.html.twig', [
                'orders' => $orders,
                'rows' => $rows,
            ]);
        }

        return new Response(null, Response::HTTP_BAD_REQUEST);
    }

    #[Route('/bestellungen/', name: 'orders', methods: ['GET'])]
    public function orders(Request $request, EntityManagerInterface $entityManager, PayhistoryRepository $payhistoryRepository): Response
    {
        $preorders = $entityManager
            ->getRepository(Orders::class)
            ->createQueryBuilder('o')
            ->select('o.for_date')
            ->addSelect('o.note')
            ->addSelect('count(o.id) as cnt')
            ->addSelect('count(o.orderedby) as personen')
            ->innerJoin('o.hirsch', 'h')
            ->addSelect('h.name')
            ->where('o.for_date > :date')
            ->groupBy('h.name')
            ->addGroupBy('o.note')
            ->orderBy('o.for_date', 'ASC')
            ->setParameter('date', strftime('%Y-%m-%d'))
            ->getQuery()
            ->getResult();
        $orderNameList = $entityManager
            ->getRepository(Orders::class)
            ->createQueryBuilder('o')
            ->select('o.orderedby')
            ->addSelect('o.id')
            ->addSelect('o.note')
            ->innerJoin('o.hirsch', 'h')
            ->addSelect('h.name')
            ->where('o.for_date = :date')
            ->setParameter('date', strftime('%Y-%m-%d'))
            ->getQuery()
            ->getResult();

        $drawn = null;

        $activePayer = $payhistoryRepository->findActivePayer();
        $cache = new FilesystemAdapter();
        $drawn = $cache->getItem('spinthewheel')->get();

        return $this->render('order/orders.html.twig', [
            'preorders' => $preorders,
            'orderNameList' => $orderNameList,
            'ordererName' => $request->cookies->get('ordererName'),
            'drawn' => $drawn,
            'activePayer' => $activePayer,
        ]);
    }

    #[Route('/zahlen-bitte/', name: 'paynow', methods: ['GET', 'POST'])]
    public function paynow(
        Request $request,
        EntityManagerInterface $entityManager,
        PaypalmesRepository $paypalmesRepository,
        PayhistoryRepository $payhistoryRepository
    ): Response {
        if ($request->isMethod('POST')) {
            $payhistory = new Payhistory();
            $payhistory->setCreated(new DateTime());
            $paypalme = $paypalmesRepository->findOneBy(['id' => $request->request->get('id')]);
            $payhistory->setPaypalme($paypalme);
            $payhistory->setClickedBy($request->cookies->get('ordererName') ?? '');

            $entityManager->persist($payhistory);
            $entityManager->flush();

            // redirect to paypalme.link
            return $this->redirect(($paypalme?->getLink() ?? 'https://paypal.me/rindulalp').'/'.(3.5 + max(0, (float) $request->request->get('tip'))));
        }

        // find all PaypalMes
        $paypalMes = $paypalmesRepository
            ->createQueryBuilder('p')
            ->select('p')
            ->getQuery()
            ->getResult();

        $active = $payhistoryRepository->findActivePayer();

        if (is_array($active) && array_key_exists('id', $active)) {
            $active = $active['id'];
        } else {
            $active = null;
        }

        return $this->render('order/paynow.html.twig', [
            'paypalmes' => $paypalMes,
            'active' => $active,
        ]);
    }

    private function getActivePayer(PaypalMesRepository $paypalmesRepository, PayhistoryRepository $payhistoryRepository, TranslatorInterface $translator): string
    {
        $activePayer = $payhistoryRepository->findActivePayer();

        if (null !== $activePayer) {
            $activePayer = $paypalmesRepository->find($activePayer['id']);

            if (null !== $activePayer) {
                $activePayer = $activePayer->getName();
            }
        }

        return $activePayer ?? $translator->trans('order.orderer');
    }
}
