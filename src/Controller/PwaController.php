<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Controller;

use App\Service\UtilityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PwaController extends AbstractController
{
    #[Route('/manifest.json', methods: ['GET'])]
    public function manifest(Request $request): JsonResponse
    {
        $jsonResponse = $this->json([
            'lang' => 'de-DE',
            'name' => 'Hirsch Bestellsammelseite',
            'short_name' => 'Hirsch Bestellung',
            'description' => sprintf(
                'Die Bestellsammelseite für den Hirsch. Aktuelle Version: %s',
                $_ENV['APP_VERSION']
            ),
            'icons' => [
                [
                    'src' => 'favicon.png',
                    'type' => 'image/png',
                    'sizes' => '512x512',
                    'purpose' => 'any maskable',
                ],
            ],
            'shortcuts' => [
                [
                    'name' => 'Tagesessen bestellen',
                    'url' => '/order/0/tagesessen?mtm_campaign=AppShortcut',
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
        $jsonResponse->setEtag(md5($jsonResponse->getContent()));
        $jsonResponse->setPublic();
        $jsonResponse->isNotModified($request);

        return $jsonResponse;
    }

    #[Route('/sw.js', methods: ['GET'])]
    public function serviceWorker(UtilityService $utilityService, Request $request): Response
    {
        $response = new Response(
            null,
            200,
            ['Content-Type' => 'application/javascript']
        );

        $response = $this->render('serviceworker.js', [
            'version' => (version_compare(
                $_ENV['APP_VERSION'],
                '2.0.0',
                '>='
            ) ? $_ENV['APP_VERSION'] : $utilityService->hashDirectory(sprintf('%s/../../public', __DIR__))),
        ], $response);
        $response->setEtag(md5($response->getContent()));
        $response->setPublic();
        $response->isNotModified($request);

        return $response;
    }
}
