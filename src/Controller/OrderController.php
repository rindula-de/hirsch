<?php

namespace App\Controller;

use App\Entity\Hirsch;
use App\Entity\Orders;
use App\Form\OrderType;
use App\Repository\HirschRepository;
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
        $order->setCreated(new DateTime())->setFor(new DateTime("+$preorder day"))->setName($hirsch);

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        // dd($form);
        if ($form->isSubmitted()) {
            $order = $form->getData();
            //dd($order);
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();
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
}
