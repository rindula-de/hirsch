<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Controller;

use App\Repository\HolidaysRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModalController extends AbstractController
{
    private SessionInterface $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    #[Route('/modalInformationText', name: 'modal', methods: ['GET'])]
    public function index(HolidaysRepository $holidaysRepository, TranslatorInterface $translator): Response
    {
        // if the current date is between the start and end date of holidays, the modal will be displayed
        $date = new \DateTime();

        // get all holidays
        $holidays = $holidaysRepository->findAll();

        // check if the current date is between the start and end date of holidays
        foreach ($holidays as $holiday) {
            // nullcheck for start and end date
            if (null !== $holiday->getStart() && null !== $holiday->getEnd()) {
                if (
                    $date >= $holiday->getStart()
                    && $date <= $holiday->getEnd()
                    && $this->session->get('last_showed_holidays', 0) < time() - 60 * 15
                ) {
                    $this->session->set('last_showed_holidays', time());

                    return new Response(
                        $translator->trans('holidays.in_holiday', [
                            '%start%' => $holiday->getStart()->format('d.m.Y'),
                            '%end%' => $holiday->getEnd()->format('d.m.Y'),
                        ])
                    );
                }
            }
        }

        return new Response();
    }
}
