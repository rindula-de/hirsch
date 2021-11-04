<?php

namespace App\Controller;

use App\Entity\Hirsch;
use App\Entity\Orders;
use App\Form\OrderType;
use App\Repository\HirschRepository;
use App\Repository\OrdersRepository;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class OrderController extends AbstractController
{
    /**
     * @Route("/order/{preorder}/{slug}", name="order")
     */
    public function index(int $preorder, String $slug, HirschRepository $hirschRepository, Request $request): Response
    {
        $order = new Orders();
        $hirsch = $hirschRepository->findOneBy(['slug' => $slug]);
        $order->setCreated((new DateTime())->setTimezone(new \DateTimeZone("Europe/Berlin")))->setForDate((new DateTime("+$preorder day"))->setTimezone(new \DateTimeZone("Europe/Berlin"))->setTime(0,0))->setHirsch($hirsch);

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        // dd($form);
        if ($form->isSubmitted()) {
            $order = $form->getData();
//            dd($order);
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();
            return $this->redirectToRoute("paynow");
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'meal' => $hirsch,
        ]);
    }
    /**
     * @Route("/order-until", name="order-until")
     */
    public function orderuntil(): Response
    {
        return new Response("Bestellungen am selben Tag bis 10:55 möglich", 200);
    }

    /**
     * @Route("/bestellungen/", name="orders")
     */
    public function orders(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $orders = $entityManager
            ->getRepository(Orders::class)
            ->createQueryBuilder("o")
            ->select('o.for_date')
            ->addSelect('o.note')
            ->addSelect('count(o.id) as cnt')
            ->addSelect('count(o.orderedby) as personen')
            ->innerJoin('o.hirsch', 'h')
            ->addSelect('h.name')
            ->where("o.for_date = :date")
            ->groupBy("h.name")
            ->addGroupBy("o.note")
            ->setParameter("date", strftime("%Y-%m-%d"))
            ->getQuery()
            ->getResult();
        $preorders = $entityManager
            ->getRepository(Orders::class)
            ->createQueryBuilder("o")
            ->select('o.for_date')
            ->addSelect('o.note')
            ->addSelect('count(o.id) as cnt')
            ->addSelect('count(o.orderedby) as personen')
            ->innerJoin('o.hirsch', 'h')
            ->addSelect('h.name')
            ->where("o.for_date > :date")
            ->groupBy("h.name")
            ->addGroupBy("o.note")
            ->orderBy("o.for_date", "ASC")
            ->setParameter("date", strftime("%Y-%m-%d"))
            ->getQuery()
            ->getResult();
        $orderNameList = $entityManager
            ->getRepository(Orders::class)
            ->createQueryBuilder("o")
            ->select('o.orderedby')
            ->where("o.for_date = :date")
            ->setParameter("date", strftime("%Y-%m-%d"))
            ->getQuery()
            ->getResult();

        return $this->render('order/orders.html.twig', [
            'orders' => $orders,
            'preorders' => $preorders,
            'orderNameList' => $orderNameList,
        ]);
    }

}
