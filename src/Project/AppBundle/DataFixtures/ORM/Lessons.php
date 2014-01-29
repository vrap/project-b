<?php

namespace Project\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Project\AppBundle\Entity\Lesson;

class Lessons implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $lessons   = array();
        $lessons []= array(
            'name'      => 'Wordpress',
            'startDate' => new \DateTime(),
            'endDate'   => new \DateTime(),
            'timecard'  => false,
        );

        foreach ($lessons as $lesson) {
            $newLesson = new Lesson();
            
            $newLesson->setName($lesson['name']);
            $newLesson->setStartDate($lesson['startDate']);
            $newLesson->setEndDate($lesson['endDate']);
            $newLesson->setTimecard($lesson['timecard']);

            $manager->persist($newLesson);
        }

        $manager->flush();
    }
}