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
        $css = file_get_contents(__DIR__ . '/../../assets/styles/app.scss');
        $css = preg_replace('/\s+/', '', $css);
        $css = preg_replace('/\/\/.*/', '', $css);
        $css = preg_replace('/\/\*[^\*]*\*\//', '', $css);
        $css = preg_replace('/@import.*;/', '', $css);
        $themecolor = explode(":", explode(';', $css)[0])[1];

        return new JsonResponse([
            "lang" => "de-DE",
            "name" => "Hirsch Bestellsammelseite",
            "short_name" => "Hirsch Bestellung",
            "description" => "Die Bestellsammelseite für den Hirsch",
            "icons" => [[
                "src" => "favicon.png",
                "type" => "image/png",
                "sizes" => "512x512",
                "purpose" => "any maskable"
            ]],
            "background_color" => "#adadad",
            "theme_color" => $themecolor,
            "start_url" => $this->generateUrl('menu'),
            "display" => "standalone",
            "orientation" => "portrait"
            ], 200, ['Content-Type' => 'application/manifest+json']);
    }

    /**
     * @Route("/sw.js", methods={"GET"})
     */
    public function serviceWorker(UtilityService $utilityService): Response
    {

        // read /public/build/manifest.json and parse it
        $manifest = json_decode(file_get_contents(__DIR__ . '/../../public/build/manifest.json'), true);

        $urlsToCache = [
            '/karte',
            '/favicon.png',
            '/api/doc',
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

        // merge $manifest and $urlsToCache
        $urlsToCache = array_merge(array_values($manifest), $urlsToCache);

        $response = new Response(
            null,
            200,
            ['Content-Type' => 'application/javascript']
        );

        return $this->render('serviceworker.js', [
            'version' => $_ENV['APP_VERSION'] ?? $utilityService->hashDirectory(__DIR__."/../../public/build") ?? '0.0.0',
            'urlsToCache' => $urlsToCache,
            'credentials' => [
                'username' => $_ENV['HT_USERNAME'] ?? '',
                'password' => $_ENV['HT_PASSWORD'] ?? '',
                'string' => base64_encode(($_ENV['HT_USERNAME'] ?? '') . ':' . ($_ENV['HT_PASSWORD'] ?? '')),
            ]
        ], $response);
    }
}
