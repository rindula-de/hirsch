<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PwaController extends AbstractController
{
    /**
     * @Route("/manifest.json")
     */
    public function index(): JsonResponse
    {
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
            "theme_color" => "#ffa303",
            "start_url" => $this->generateUrl('menu'),
            "display" => "standalone",
            "orientation" => "portrait"
            ], 200, ['Content-Type' => 'application/manifest+json']);
    }
}
