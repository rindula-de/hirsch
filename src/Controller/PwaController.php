<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Controller;

use App\Service\UtilityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class PwaController extends AbstractController
{
    #[Route('/manifest.json', methods: ['GET'])]
    public function manifest(MessageBusInterface $bus): JsonResponse
    {
        return $this->json([
            'lang' => 'de-DE',
            'name' => 'Hirsch Bestellsammelseite',
            'short_name' => 'Hirsch Bestellung',
            'description' => sprintf('Die Bestellsammelseite für den Hirsch. Aktuelle Version: %s', $_ENV['APP_VERSION']),
            'icons' => [[
                'src' => 'favicon.png',
                'type' => 'image/png',
                'sizes' => '512x512',
                'purpose' => 'any maskable',
            ]],
            'shortcuts' => [
                [
                    'name' => 'Tagesessen bestellen',
                    'url' => '/order/0/tagesessen',
                    'description' => 'Komme direkt auf die Tagesessenbestellseite',
                ],
            ],
            'background_color' => '#adadad',
            'theme_color' => '#ffa303',
            'start_url' => $this->generateUrl('menu'),
            'display' => 'standalone',
            'version' => $_ENV['APP_VERSION'] ?? '99.99.99',
            'version_name' => $_ENV['APP_VERSION'] ?? 'Development',
            'orientation' => 'portrait',
            'manifest_version' => 3,
        ], 200, ['Content-Type' => 'application/manifest+json']);
    }

    #[Route('/sw.js', methods: ['GET'])]
    public function serviceWorker(UtilityService $utilityService): Response
    {
        $response = new Response(
            null,
            200,
            ['Content-Type' => 'application/javascript']
        );

        return $this->render('serviceworker.js', [
            'version' => (version_compare($_ENV['APP_VERSION'], '2.0.0', '>=') ? $_ENV['APP_VERSION'] : $utilityService->hashDirectory(__DIR__.'/../../public')),
        ], $response);
    }
}
