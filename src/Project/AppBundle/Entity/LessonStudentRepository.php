<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Lesson_has_StudentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Lesson_has_StudentRepository extends EntityRepository
{
    public function findStudentsByLesson($lesson_id)
    {
        $em = $this->getEntityManager();

        return $em->createQuery(
                    '
                    SELECT student_id
                    FROM ProjectAppBundle:Lesson_has_student
                    WHERE lesson_id = :id
                    '
                )
                ->setParameter('id', $lesson_id)
                ->getArrayResult();
    }
}
