<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Controller;

use App\Entity\Holidays;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ModalController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private SessionInterface $session;

    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack)
    {
        $this->doctrine = $doctrine;
        $this->session = $requestStack->getSession();
    }

    #[Route('/modalInformationText/{id}', name: 'modal', methods: ['GET'])]
    public function index(): Response
    {
        // if the current date is between the start and end date of holidays, the modal will be displayed
        $date = new \DateTime();
        // get all holidays
        $holidays = $this->doctrine->getRepository(Holidays::class)->findAll();
        // check if the current date is between the start and end date of holidays
        foreach ($holidays as $holiday) {
            // nullcheck for start and end date
            if ($holiday->getStart() !== null && $holiday->getEnd() !== null) {
                if ($date >= $holiday->getStart() && $date <= $holiday->getEnd() && $this->session->get('last_showed_holidays', 0) < time() - 60 * 15) {
                    $this->session->set('last_showed_holidays', time());

                    return new Response('Der Hirsch hat aktuell Urlaub und ist vom '.$holiday->getStart()->format('d.m.Y').' bis zum '.$holiday->getEnd()->format('d.m.Y').' nicht verfügbar. Bestellungen können danach wieder aufgenommen werden!');
                }
            }
        }

        return new Response();
    }
}
