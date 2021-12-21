<?php

namespace App\Controller;

use App\Entity\Payhistory;
use App\Entity\Paypalmes;
use App\Form\PaypalmesType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaypalMesController extends AbstractController
{
    #[Route('/paypal/add', name: 'paypal_add')]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        // load form
        $form = $this->createForm(PaypalmesType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // get data
            $data = $form->getData();
            // save data
            $em = $doctrine->getManager();
            $em->persist($data);
            $em->flush();
            // redirect
            return $this->redirectToRoute('paynow');
        }

        return $this->render('paypal_mes/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/paypal/edit/{id}', name: 'paypal_edit')]
    public function edit(Paypalmes $paypalme, Request $request, ManagerRegistry $doctrine): Response
    {
        // load form
        $form = $this->createForm(PaypalmesType::class, $paypalme);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // get data
            $data = $form->getData();
            // save data
            $em = $doctrine->getManager();
            $em->persist($data);
            $em->flush();
            // redirect
            return $this->redirectToRoute('paynow');
        }

        return $this->render('paypal_mes/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/paypal/remove-active/{id}', name: 'paypal_remove_active')]
    public function remove_active(Paypalmes $paypalme, Request $request, ManagerRegistry $doctrine): Response
    {
        // remove entries from database
        $em = $doctrine->getManager();
        $payhistory = $em->getRepository(Payhistory::class)->findBy(['paypalme' => $paypalme]);
        foreach ($payhistory as $pay) {
            $em->remove($pay);
        }
        $em->flush();
        return $this->redirectToRoute('paynow');
    }
}
