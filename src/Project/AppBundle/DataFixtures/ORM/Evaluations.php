<?php

namespace Project\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Project\AppBundle\Entity\Evaluation;

class Evaluations/* implements FixtureInterface*/
{
    /*public function load(ObjectManager $manager)
    {
        $modules   = array();
        $modules []= array(
            'name' => 'CMS',
        );

        foreach ($modules as $module) {
            $newModule = new Evaluation();

            $newModule->setDescription($module['name']);

            $manager->persist($newModule);
        }

        $manager->flush();
    }*/
}