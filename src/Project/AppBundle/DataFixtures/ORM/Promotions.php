<?php

namespace Project\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Project\AppBundle\Entity\Promotion;

class Promotions implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $promotions   = array();
        $promotions []= array(
            'startDate' => new \DateTime(),
            'endDate'   => new \DateTime(),
        );

        foreach ($promotions as $promotion) {
            $newPromotion = new Promotion();
            
            $newPromotion->setStartDate($promotion['startDate']);
            $newPromotion->setEndDate($promotion['endDate']);

            $manager->persist($newPromotion);
        }

        $manager->flush();
    }
}