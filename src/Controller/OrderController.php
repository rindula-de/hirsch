<?php

namespace App\Controller;

use App\Entity\Hirsch;
use App\Entity\Orders;
use App\Entity\Payhistory;
use App\Entity\Paypalmes;
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

    /**
     * @Route("/zahlen-bitte/", name="paynow")
     */
    public function paynow(Request $request): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $payhistory = new Payhistory();
            $payhistory->setCreated(new DateTime());
            $paypalme = $entityManager->getRepository(Paypalmes::class)->findOneBy(['id' => $request->request->get('id')]);
            $payhistory->setPaypalme($paypalme);
            $entityManager->persist($payhistory);
            $entityManager->flush();
            // redirect to paypalme.link
            return $this->redirect($paypalme->getLink());
        }

        // find all PaypalMes
        $paypalMes = $entityManager
            ->getRepository(Paypalmes::class)
            ->createQueryBuilder("p")
            ->select('p')
            ->getQuery()
            ->getResult();
        // get most common payhistory.paypalme
        $active = $entityManager
            ->getRepository(Payhistory::class)
            ->createQueryBuilder("p")
            ->select('count(p.paypalme) as cnt')
            ->join('p.paypalme', 'pm')
            ->addSelect('pm.id')
            ->where("p.created BETWEEN :date_start AND :date_end")
            ->groupBy("p.paypalme")
            ->orderBy("cnt", "DESC")
            ->setParameter("date_start", strftime("%Y-%m-%d").' 00:00:00')
            ->setParameter("date_end", strftime("%Y-%m-%d").' 23:59:59')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()[0]['id'];
        
        return $this->render('order/paynow.html.twig', [
            'paypalmes' => $paypalMes,
            'active' => $active,
        ]);
    }

}
