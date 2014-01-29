<?php

namespace Project\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Project\AppBundle\Entity\Formation;

class Formations implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $formations   = array();
        $formations []= array(
            'name' => 'Licence professionnelle',
        );

        foreach ($formations as $formation) {
            $newFormation = new Formation();
            
            $newFormation->setName($formation['name']);

            $manager->persist($newFormation);
        }

        $manager->flush();
    }
}