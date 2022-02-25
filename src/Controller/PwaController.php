<?php

/*
 * (c) Sven Nolting, 2022
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
        // read /assets/styles/app.scss and use regex to extract the CSS
        $css = file_get_contents(__DIR__.'/../../assets/styles/app.scss');
        if ($css) {
            $css = preg_replace('/\s+/', '', $css);
            $css = preg_replace('/\/\/.*/', '', $css ?? '');
            $css = preg_replace('/\/\*[^\*]*\*\//', '', $css ?? '');
            $css = preg_replace('/@import.*;/', '', $css ?? '');
            $themecolor = explode(':', explode(';', $css ?? ':')[0])[1];
        } else {
            $themecolor = '#3f51b5';
        }

        $cache = new FilesystemAdapter();
        $cache->get('msuser_cache', function (ItemInterface $item) use ($bus) {
            // set $time to next noon
            $time = new DateTime('now');
            $time->setTime(12, 0, 0);
            // if $time is in past, set $time to next day
            if ($time < new DateTime('now')) {
                $time->modify('+1 day');
            }
            // $time to seconds
            $time = $time->getTimestamp() - time();

            $item->expiresAfter(43200 + $time);
            print_r($time);
            $bus->dispatch(new FetchMsUsers(), [new DelayStamp($time * 1000)]);

            return null;
        });

        return new JsonResponse([
            'lang'        => 'de-DE',
            'name'        => 'Hirsch Bestellsammelseite '.$_ENV['APP_VERSION'],
            'short_name'  => 'Hirsch Bestellung',
            'description' => 'Die Bestellsammelseite für den Hirsch. Aktuelle Version: '.$_ENV['APP_VERSION'],
            'icons'       => [[
                'src'     => 'favicon.png',
                'type'    => 'image/png',
                'sizes'   => '512x512',
                'purpose' => 'any maskable',
            ]],
            'shortcuts' => [
                [
                    'name'        => 'Tagesessen bestellen',
                    'url'         => '/order/0/tagesessen',
                    'description' => 'Komme direkt auf die Tagesessenbestellseite',
                ],
            ],
            'background_color' => '#adadad',
            'theme_color'      => $themecolor,
            'start_url'        => $this->generateUrl('menu'),
            'display'          => 'standalone',
            'orientation'      => 'portrait',
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
            'version'       => (version_compare(ltrim($_ENV['APP_VERSION'], " \n\r\t\v\x00v"), '2.0.0', '>=') >= 0 ? $_ENV['APP_VERSION'] : $utilityService->hashDirectory(__DIR__.'/../../public')),
        ], $response);
    }
}
