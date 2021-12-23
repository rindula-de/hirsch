<?php

namespace App\Controller;

use App\Service\UtilityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PwaController extends AbstractController
{
    /**
     * @Route("/manifest.json", methods={"GET"})
     */
    public function manifest(): JsonResponse
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

    /**
     * @Route("/sw.js", methods={"GET"})
     */
    public function serviceWorker(UtilityService $utilityService): Response
    {
        $urlsToCache = [
            $this->generateUrl('menu'),
            '/favicon.png',
            $this->generateUrl('offlineinfo'),
            'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js',
            'https://cdn.jsdelivr.net/npm/flatpickr',
            'https://fonts.googleapis.com/icon?family=Material+Icons',
            'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
            'https://fonts.googleapis.com/css?family=Raleway:400,700',
            'https://fonts.gstatic.com/s/raleway/v22/1Ptug8zYS_SKggPNyCAIT5lu.woff2',
            'https://fonts.gstatic.com/s/raleway/v22/1Ptug8zYS_SKggPNyCkIT5lu.woff2',
            'https://fonts.gstatic.com/s/raleway/v22/1Ptug8zYS_SKggPNyCIIT5lu.woff2',
            'https://fonts.gstatic.com/s/raleway/v22/1Ptug8zYS_SKggPNyCMIT5lu.woff2',
            'https://fonts.gstatic.com/s/raleway/v22/1Ptug8zYS_SKggPNyC0ITw.woff2',
        ];

        // read /public/build/manifest.json and parse it
        $manifest_json = file_get_contents(__DIR__.'/../../public/build/manifest.json');
        if ($manifest_json) {
            $manifest = json_decode($manifest_json, true);
        } else {
            $manifest = [];
        }

        // merge $manifest and $urlsToCache
        $urlsToCache = array_merge(array_values($manifest), $urlsToCache);

        $response = new Response(
            null,
            200,
            ['Content-Type' => 'application/javascript']
        );

        return $this->render('serviceworker.js', [
            'version'       => ($_ENV['APP_VERSION'] !== 'development' ? $_ENV['APP_VERSION'] : $utilityService->hashDirectory(__DIR__.'/../../public')),
            'urlsToCache'   => json_encode($urlsToCache),
            'offline_route' => $this->generateUrl('offlineinfo'),
            'credentials'   => [
                'username' => $_ENV['HT_USERNAME'] ?? '',
                'password' => $_ENV['HT_PASSWORD'] ?? '',
                'string'   => base64_encode(($_ENV['HT_USERNAME'] ?? '').':'.($_ENV['HT_PASSWORD'] ?? '')),
            ],
        ], $response);
    }

    /**
     * @Route("/offline", name="offlineinfo")
     */
    public function offlineInfo(): Response
    {
        return new Response('Du bist offline', 200);
    }
}
