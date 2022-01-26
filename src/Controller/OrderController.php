<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Payhistory;
use App\Entity\Paypalmes;
use App\Form\OrderType;
use App\Repository\HirschRepository;
use App\Repository\OrdersRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/order/{preorder}/{slug}", name="order", methods={"GET", "POST"})
     */
    public function index(int $preorder, string $slug, HirschRepository $hirschRepository, Request $request, ManagerRegistry $doctrine): Response
    {
        $order = new Orders();
        $hirsch = $hirschRepository->findOneBy(['slug' => $slug]);
        if ($hirsch === null) {
            return $this->redirectToRoute('menu');
        }
        $preorder_time = (new DateTime("+$preorder day"))->setTimezone(new \DateTimeZone('Europe/Berlin'))->setTime(0, 0);
        $order->setCreated((new DateTime())->setTimezone(new \DateTimeZone('Europe/Berlin')))->setForDate($preorder_time)->setHirsch($hirsch);
        if ($request->cookies->get('ordererName')) {
            $order->setOrderedby($request->cookies->get('ordererName'));
        }
        $form = $this->createForm(OrderType::class, $order, ['for_date' => $order->getForDate()?->format('d.m.Y') ?? (new \DateTime('now'))->format('d.m.Y')]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $order = $form->getData();
            $em = $doctrine->getManager();
            $response = new RedirectResponse($this->generateUrl('paynow'));
            if ($order instanceof Orders) {
                $em->persist($order);
                $em->flush();
                // Set ordererName Cookie
                $cookie = new Cookie('ordererName', $order->getOrderedby(), (new DateTime('+1 year'))->setTimezone(new \DateTimeZone('Europe/Berlin')));

                // create response with cookie
                $response->headers->setCookie($cookie);
                // redirect with cookie
            }

            return $response;
        }

        return $this->render('order/index.html.twig', [
            'form'       => $form->createView(),
            'meal'       => $hirsch,
            'order_date' => $preorder_time,
        ]);
    }

    /**
     * @Route("/order-until", name="order-until", methods={"GET"})
     */
    public function orderuntil(): Response
    {
        return new Response('Bestellungen am selben Tag bis 10:55 möglich', 200);
    }

    // function to delete order
    /**
     * @Route("/orders/delete/{id}", name="order_delete", methods={"GET", "DELETE"})
     */
    public function delete(Orders $order, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($order);
        $entityManager->flush();
        $this->addFlash('success', 'Bestellung gelöscht');

        return $this->redirectToRoute('orders');
    }

    public function getOrdersData(OrdersRepository $ordersRepository, bool $onlyToday = true): array<mixed>
    {
        $orders = $ordersRepository->findAll();
        $data = [];
        foreach ($orders as $order) {
            if (!$onlyToday || ($order->getForDate() && $order->getForDate()->format('Y-m-d') === (new DateTime())->format('Y-m-d'))) {
                $data[] = [
                    'id'          => $order->getId(),
                    'orderedby'   => $order->getOrderedby(),
                    'created'     => $order->getCreated(),
                    'forDate'     => $order->getForDate(),
                    'note'        => $order->getNote(),
                    'ordered'     => $order->getHirsch()?->getName(),
                    'orderedSlug' => $order->getHirsch()?->getSlug(),
                ];
            }
        }

        return $data;
    }

    /**
     * Get a list of all orders today.
     *
     * @Route("/api/orders/{onlyToday?1}", name="api_orders", methods={"GET"})
     *
     * @param bool $onlyToday
     * @OA\Parameter(
     *     name="onlyToday",
     *     in="path",
     *     description="Nur Bestellungen für heute anzeigen = 1; Alle Bestellungen anzeigen = 0",
     *     @OA\Schema(type="integer")
     * )
     */
    public function api_orders(OrdersRepository $ordersRepository, bool $onlyToday = true): JsonResponse
    {
        $data = $this->getOrdersData($ordersRepository, $onlyToday);

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/orders/stream", methods={"GET"})
     */
    public function api_orders_stream(OrdersRepository $ordersRepository): StreamedResponse
    {
        $response = new StreamedResponse();
        $response->setCallback(function () {
            echo json_encode($this->getOrdersData($ordersRepository, true));
            flush();
        });
        $response->send();
    }

    /**
     * @Route("/bestellungen/", name="orders", methods={"GET"})
     */
    public function orders(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $orders = $entityManager
            ->getRepository(Orders::class)
            ->createQueryBuilder('o')
            ->select('o.for_date')
            ->addSelect('o.note')
            ->addSelect('count(o.id) as cnt')
            ->addSelect('count(o.orderedby) as personen')
            ->innerJoin('o.hirsch', 'h')
            ->addSelect('h.name')
            ->where('o.for_date = :date')
            ->groupBy('h.name')
            ->addGroupBy('o.note')
            ->setParameter('date', strftime('%Y-%m-%d'))
            ->getQuery()
            ->getResult();
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

        return $this->render('order/orders.html.twig', [
            'orders'        => $orders,
            'preorders'     => $preorders,
            'orderNameList' => $orderNameList,
            'ordererName'   => $request->cookies->get('ordererName'),
        ]);
    }

    /**
     * @Route("/zahlen-bitte/", name="paynow", methods={"GET", "POST"})
     */
    public function paynow(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        if ($request->isMethod('POST')) {
            $payhistory = new Payhistory();
            $payhistory->setCreated(new DateTime());
            $paypalme = $doctrine->getRepository(Paypalmes::class)->findOneBy(['id' => $request->request->get('id')]);
            $payhistory->setPaypalme($paypalme);
            $entityManager->persist($payhistory);
            $entityManager->flush();
            // redirect to paypalme.link
            return $this->redirect(($paypalme?->getLink() ?? 'https://paypal.me/rindulalp').'/'.(3.5 + $request->request->get('tip')));
        }

        // find all PaypalMes
        $paypalMes = $entityManager
            ->getRepository(Paypalmes::class)
            ->createQueryBuilder('p')
            ->select('p')
            ->getQuery()
            ->getResult();
        // get most common payhistory.paypalme
        $active = $entityManager
            ->getRepository(Payhistory::class)
            ->createQueryBuilder('p')
            ->select('count(p.paypalme) as cnt')
            ->join('p.paypalme', 'pm')
            ->addSelect('pm.id')
            ->where('p.created BETWEEN :date_start AND :date_end')
            ->groupBy('p.paypalme')
            ->orderBy('cnt', 'DESC')
            ->setParameter('date_start', strftime('%Y-%m-%d').' 00:00:00')
            ->setParameter('date_end', strftime('%Y-%m-%d').' 23:59:59')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        if (is_array($active) && array_key_exists(0, $active) && array_key_exists('id', $active[0])) {
            $active = $active[0]['id'];
        } else {
            $active = null;
        }

        return $this->render('order/paynow.html.twig', [
            'paypalmes' => $paypalMes,
            'active'    => $active,
        ]);
    }
}
