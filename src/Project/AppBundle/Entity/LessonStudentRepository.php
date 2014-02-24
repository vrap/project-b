<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Lesson_has_StudentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LessonStudentRepository extends EntityRepository
{
    /**
     * Find all students registered for a lesson
     *
     * @param $lesson_id
     * @return array
     */
    public function findStudentsByLessonId($lesson_id)
    {
        $em = $this->getEntityManager();

        return $em->createQuery(
                '
                SELECT ls.studentUserId
                FROM ProjectAppBundle:LessonStudent ls
                WHERE ls.lessonId = :id
                '
            )
            ->setParameter('id', $lesson_id)
            ->getArrayResult();
    }

    /**
     * Find lesson_has_student
     *
     * @param $student_id
     * @param $lesson_id
     * @return mixed
     */
    public function findOneByLessonStudent($student_id, $lesson_id)
    {
        return $this->getEntityManager()
                ->createQuery(
                'SELECT ls
                FROM ProjectAppBundle:LessonStudent ls
                WHERE ls.lessonId = :lesson
                AND ls.studentUserId = :student'
                )
               ->setParameters(array(
                    'lesson' => $lesson_id,
                    'student' => $student_id
                ))
               ->getSingleResult();
    }

    /**
     * Set student absent for a lesson
     *
     * @param $student_id
     * @param $lesson_id
     * @return bool
     */
    public function setAbsent($student_id, $lesson_id)
    {
        $em = $this->getEntityManager();
        $lesson = $this->findOneByLessonStudent($student_id, $lesson_id);
        if(!$lesson)
        {
            return false;
        }
        $lesson->setAbsent(true);
        $em->flush();

        return true;
    }
}
