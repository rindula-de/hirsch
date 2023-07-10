<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Controller;

use App\Entity\Holidays;
use App\Form\HolidayType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HolidayController extends AbstractController
{
    #[Route('/admin/holidays', name: 'holidays', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {
        $holidays = $doctrine->getRepository(Holidays::class)->findAll();

        return $this->render('holiday/index.html.twig', [
            'holidays' => $holidays,
        ]);
    }

    /**
     * Get all holidays.
     */
    #[Route('/api/holidays', name: 'holidays_api', methods: ['GET'])]
    public function holidays(ManagerRegistry $doctrine): JsonResponse
    {
        $holidays = $doctrine
            ->getRepository(Holidays::class)
            ->findAll();

        return $this->json($holidays);
    }

    #[Route('/admin/holidays/edit/{id}', name: 'holidays_edit', methods: ['GET', 'POST'])]
    public function edit(Holidays $holiday, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);

        // save holiday
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('holidays');
        }

        return $this->render('holiday/edit.html.twig', [
            'holiday' => $holiday,
            'form' => $form,
        ]);
    }

    #[Route('/admin/holidays/add', name: 'holidays_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $holiday = new Holidays();
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($holiday);
            $entityManager->flush();

            return $this->redirectToRoute('holidays');
        }

        return $this->render('holiday/add.html.twig', [
            'holiday' => $holiday,
            'form' => $form,
        ]);
    }
}
