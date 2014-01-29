<?php

namespace Project\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Project\AppBundle\Entity\User;

class Users implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $users   = array();
        $users []= array(
            'name'     => 'Pierre',
            'surname'  => 'Flauder',
            'email'    => 'pflauder@gmail.com',
            'phone'    => '',
            'login'    => 'pierre',
            'password' => 'pierre',
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