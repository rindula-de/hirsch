<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\DataFixtures;

use App\Entity\Hirsch;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HTGFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $htg = new Hirsch();

        $htg->setName('Tagesessen');
        $htg->setSlug('tagesessen');
        $htg->setDisplay(true);

        $manager->persist($htg);

        // //////////////////////

        $htg = new Hirsch();

        $htg->setName('Schweizer Wurstsalat mit Pommes');
        $htg->setSlug('Schweizer-Wurstsalat-mit-Pommes');
        $htg->setDisplay(true);

        $manager->persist($htg);

        // ///////////////////////

        $manager->flush();
    }
}
