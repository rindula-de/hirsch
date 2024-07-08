<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Controller;

use App\Repository\DailyFoodRepository;
use App\Repository\HirschRepository;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Exception;
use Smalot\PdfParser\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('menu');
    }

    #[Route('/karte', name: 'menu', methods: ['GET'])]
    public function menu(): Response
    {
        $cache = new FilesystemAdapter();
        $menuDisabled = $cache->get('menu_disabled', function (ItemInterface $item) {
            $item->expiresAfter(0);

            return false;
        });

        return $this->render('menu/index.html.twig', [
            'menu_disabled' => $menuDisabled,
        ]);
    }

    /**
     * Get the Hirsch to Go menu.
     */
    #[Route('/api/get-menu', name: 'api_menu', methods: ['GET'])]
    public function getMenu(Request $request, HirschRepository $hirschRepository): Response
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('display', true))->andWhere(Criteria::expr()->neq('slug', 'tagesessen'));

        $htg = $hirschRepository->matching($criteria)->toArray();

        $frameId = $request->headers->get('Turbo-Frame');

        $cache = new FilesystemAdapter();
        $menuDisabled = $cache->get('menu_disabled', function (ItemInterface $item) {
            $item->expiresAfter(0);

            return false;
        });

        if (null === $frameId) {
            return $this->json($menuDisabled ? [] : $htg);
        }

        return $this->render('menu/htgframe.html.twig', [
            'htg' => $htg,
            'menu_disabled' => $menuDisabled,
        ]);
    }

    /**
     * Get a list of all menu items this week.
     */
    #[Route('/api/get-tagesessen', name: 'tagesessen', methods: ['GET'])]
    #[Route('/api/get-tagesessen-karte', name: 'tagesessenkarte', methods: ['GET'])]
    public function getTagesessen(
        Request $request,
        TranslatorInterface $translator,
        DailyFoodRepository $dailyFoodRepository): Response
    {
        $file = '';
        $message = '';

        $displayData = $dailyFoodRepository->getDailyFood();

        $frameId = $request->headers->get('Turbo-Frame');

        if (null === $frameId) {
            if ('tagesessenkarte' == $request->attributes->get('_route')) {
                return $this->json(['file' => $file, 'message' => $message]);
            }

            if ('tagesessen' == $request->attributes->get('_route')) {
                return $this->json(['displayData' => $displayData, 'message' => $message]);
            }

            return $this->json(['message' => $translator->trans('defaults.route_not_found')]);
        } else {
            if ('dailymenu' === $frameId) {
                if (is_array($displayData)) {
                    $displayData = array_filter($displayData, function ($d) {
                        return $d['date'] >= new DateTime('today');
                    });
                }

                return $this->render('menu/frame.html.twig', [
                    'dailyfood' => $displayData,
                ]);
            }

            return $this->json(['message' => $translator->trans('defaults.route_not_found')]);
        }
    }
}
