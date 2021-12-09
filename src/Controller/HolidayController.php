<?php

namespace App\Controller;

use App\Entity\Holidays;
use App\Form\HolidayType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HolidayController extends AbstractController
{
    /**
     * @Route("/holidays", name="holidays", methods={"GET"})
     */
    public function index(): Response
    {
        $holidays = $this->getDoctrine()
            ->getRepository(Holidays::class)
            ->findAll();

        return $this->render('holiday/index.html.twig', [
            'controller_name' => 'HolidayController',
            'holidays'        => $holidays,
        ]);
    }

    /**
     * Get all holidays.
     *
     * @Route("/api/holidays", name="holidays_api", methods={"GET"})
     */
    public function holidays(): JsonResponse
    {
        $holidays = $this->getDoctrine()
            ->getRepository(Holidays::class)
            ->findAll();

        return $this->json($holidays, 200);
    }

    /**
     * @Route("/holidays/edit/{id}", name="holidays_edit", methods={"GET", "POST"})
     */
    public function edit(Holidays $holiday, Request $request): Response
    {
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);
        dump($holiday);
        // save holiday
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('holidays');
        }

        return $this->render('holiday/edit.html.twig', [
            'holiday' => $holiday,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/holidays/add", name="holidays_add", methods={"GET", "POST"})
     */
    public function add(Request $request): Response
    {
        $holiday = new Holidays();
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($holiday);
            $entityManager->flush();

            return $this->redirectToRoute('holidays');
        }

        return $this->render('holiday/edit.html.twig', [
            'holiday' => $holiday,
            'form'    => $form->createView(),
        ]);
    }
}
