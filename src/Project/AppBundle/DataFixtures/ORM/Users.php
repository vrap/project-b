<?php

namespace Project\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Project\AppBundle\Entity\User;

class Users implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $users = array(
            array(
                'name'     => 'Laurent',
                'surname'  => 'Ricard',
                'email'    => 'roundandsoft@gmail.com',
                'phone'    => '',
                'login'    => 'laurent',
                'password' => 'laurent',
            ),
            array(
                'name'     => 'Emmanuelle',
                'surname'  => 'Roux',
                'email'    => 'emmanuelle.roux@gmail.com',
                'phone'    => '',
                'login'    => 'emmanuelle',
                'password' => 'emmanuelle',
            ),
        );

        foreach ($users as $user) {
            $newUser = new User();
            
            $newUser->setName($user['name']);
            $newUser->setSurname($user['surname']);
            $newUser->setEmail($user['email']);
            $newUser->setPhone($user['phone']);
            $newUser->setLogin($user['login']);
            $newUser->setPassword($user['password']);

            $manager->persist($newUser);
        }

        $manager->flush();
    }
}