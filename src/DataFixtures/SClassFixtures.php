<?php

namespace App\DataFixtures;

use App\Entity\SClass;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class SClassFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i<=15; $i++) {
            $class = new SClass;
            $class ->setName("Class $i");
            $class ->setQuantity(rand(20,30));
            $manager ->persist($class);
        }
        $manager->flush();
    }
}
