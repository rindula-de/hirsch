<?php

namespace App\Controller;

use App\Entity\Holidays;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModalController extends AbstractController
{
    /**
     * @Route("/modalInformationText", name="modal", methods={"GET"})
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        // if the current date is between the start and end date of holidays, the modal will be displayed
        $date = new \DateTime();
        // get all holidays
        $holidays = $doctrine->getRepository(Holidays::class)->findAll();
        // check if the current date is between the start and end date of holidays
        foreach ($holidays as $holiday) {
            // nullcheck for start and end date
            if ($holiday->getStart() !== null && $holiday->getEnd() !== null) {
                if ($date >= $holiday->getStart() && $date <= $holiday->getEnd()) {
                    return new Response('Der hirsch hat aktuell Urlaub und ist vom '.$holiday->getStart()->format('d.m.Y').' bis zum '.$holiday->getEnd()->format('d.m.Y').' nicht verfügbar. Bestellungen können danach wieder aufgenommen werden!');
                }
            }
        }

        return new Response();
    }
}
