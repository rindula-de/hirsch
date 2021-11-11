<?php

namespace App\Controller;

use App\Entity\Holidays;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HolidayController extends AbstractController
{
    /**
     * @Route("/holidays", name="holidays")
     */
    public function index(): Response
    {
        return $this->render('holiday/index.html.twig', [
            'controller_name' => 'HolidayController',
        ]);
    }

    /**
     * @Route("/api/holidays", name="holidays_api", methods={"GET"})
     */
    public function holidays(): JsonResponse
    {
        $holidays = $this->getDoctrine()
            ->getRepository(Holidays::class)
            ->findAll();

        return $this->json($holidays, 200);
    }
}
