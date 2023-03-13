<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Controller;

use App\Message\FetchMsUsers;
use App\Service\UtilityService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

class PwaController extends AbstractController
{
    #[Route('/manifest.json', methods: ['GET'])]
    public function manifest(MessageBusInterface $bus): JsonResponse
    {
        $cache = new FilesystemAdapter();
        $cache->get('msuser_cache', function (ItemInterface $item) use ($bus) {
            // set $time to next noon
            /** @var DateTime */
            $time = DateTime::createFromFormat('H:i', '12:00');
            // if $time is in past, set $time to next day
            if ($time < DateTime::createFromFormat('U', time().'')) {
                $time->modify('+1 day');
            }
            // $time to seconds
            $time = $time->getTimestamp() - time();

            $item->expiresAfter(43200 + $time);
            $bus->dispatch(new FetchMsUsers(), [new DelayStamp($time * 1000)]);

            return null;
        });

        return new JsonResponse([
            'lang' => 'de-DE',
            'name' => "Hirsch Bestellsammelseite",
            'short_name' => 'Hirsch Bestellung',
            'description' => sprintf("Die Bestellsammelseite für den Hirsch. Aktuelle Version: %s", $_ENV['APP_VERSION']),
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
            'version' => (version_compare(ltrim($_ENV['APP_VERSION'], " \n\r\t\v\x00v"), '2.0.0', '>=') >= 0 ? $_ENV['APP_VERSION'] : $utilityService->hashDirectory(__DIR__.'/../../public')),
        ], $response);
    }
}
