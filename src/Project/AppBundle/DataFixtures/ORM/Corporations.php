<?php

namespace Project\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Project\AppBundle\Entity\Corporation;

class Corporations implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $corporations   = array();
        $corporations []= array(
            'name'  => 'Seniormedia',
            'email' => 'seniormedia@gmail.com',
            'phone' => '',
        );

        foreach ($corporations as $corporation) {
            $newCorporation = new Corporation();
            
            $newCorporation->setName($corporation['name']);
            $newCorporation->setEmail($corporation['email']);
            $newCorporation->setPhone($corporation['phone']);

            $manager->persist($newCorporation);
        }

        $manager->flush();
    }
}