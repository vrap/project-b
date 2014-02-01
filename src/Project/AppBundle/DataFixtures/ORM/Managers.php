<?php

namespace Project\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Project\AppBundle\Entity\Manager;

class Managers implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $managers = array(

        );
        
        foreach ($managers as $manager) {
            $newUser = new User();
            
            $newUser->setName($manager['name']);

            $manager->persist($newUser);
        }

        $manager->flush();
    }
}