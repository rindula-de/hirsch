<?php

namespace App\Controller;

use App\Form\PaypalmesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaypalMesController extends AbstractController
{
    #[Route('/paypal/add', name: 'paypal_add')]
    public function add(Request $request): Response
    {
        // load form 
        $form = $this->createForm(PaypalmesType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // get data
            $data = $form->getData();
            // save data
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
            // redirect
            return $this->redirectToRoute('paynow');
        }

        return $this->render('paypal_mes/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
