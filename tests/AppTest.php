<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class AppTest extends WebTestCase
{
    public function testAllPages(): void
    {

        $client = static::createClient();
        
        $routes = [
            "/karte",
            "/bestellungen/",
            "/zahlen-bitte/",
            "/paypal/add",
            "/paypal/edit/1",
        ];
        foreach ($routes as $route) {
            $client->request('GET', $route);
            $this->assertResponseIsSuccessful();
        }
    }
}
