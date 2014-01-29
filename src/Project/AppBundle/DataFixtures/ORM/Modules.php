<?php

namespace Project\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Project\AppBundle\Entity\Module;

class Modules implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $modules   = array();
        $modules []= array(
            'name' => 'CMS',
        );

        foreach ($modules as $module) {
            $newModule = new Module();
            
            $newModule->setName($module['name']);

            $manager->persist($newModule);
        }

        $manager->flush();
    }
}