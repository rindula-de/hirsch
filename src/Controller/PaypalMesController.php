<?php

namespace App\Controller;

use App\Entity\Payhistory;
use App\Entity\Paypalmes;
use App\Form\PaypalmesType;
use App\Repository\PayhistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaypalMesController extends AbstractController
{
    #[Route('/paypal/add', name: 'paypal_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaypalmesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data instanceof Paypalmes) {
                $entityManager->persist($data);
                $entityManager->flush();
            }

            return $this->redirectToRoute('paynow');
        }

        return $this->render('paypal_mes/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/paypal/edit/{id}', name: 'paypal_edit')]
    public function edit(Paypalmes $paypalme, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaypalmesType::class, $paypalme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data instanceof Paypalmes) {
                $entityManager->persist($data);
                $entityManager->flush();
            }

            return $this->redirectToRoute('paynow');
        }

        return $this->render('paypal_mes/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/paypal/remove-active/{id}', name: 'paypal_remove_active')]
    public function remove_active(Paypalmes $paypalme, EntityManagerInterface $entityManager, PayhistoryRepository $payhistoryRepository): Response
    {
        $payhistory = $payhistoryRepository->findBy(['paypalme' => $paypalme]);

        foreach ($payhistory as $pay) {
            $entityManager->remove($pay);
        }
        $entityManager->flush();

        return $this->redirectToRoute('paynow');
    }
}
