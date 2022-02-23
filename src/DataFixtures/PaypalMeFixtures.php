<?php

namespace App\DataFixtures;

use App\Entity\Paypalmes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaypalMeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $ppm = new Paypalmes();

        $ppm->setEmail('nolting@hochwarth-it.de');
        $ppm->setName('Sven Nolting');
        $ppm->setLink('https://paypal.me/rindulalp');
        $ppm->setBar(null);

        $manager->persist($ppm);

        $manager->flush();
    }
}
