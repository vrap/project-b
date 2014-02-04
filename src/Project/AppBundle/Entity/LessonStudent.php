<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lesson_has_Student
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\Lesson_has_StudentRepository")
 */
class Lesson_has_Student
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="lesson_id", type="integer")
     */
    private $lessonId;

    /**
     * @var integer
     *
     * @ORM\Column(name="student_user_id", type="integer")
     */
    private $studentUserId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="absent", type="boolean")
     */
    private $absent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="justified", type="boolean")
     */
    private $justified;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lessonId
     *
     * @param integer $lessonId
     * @return Lesson_has_Student
     */
    public function setLessonId($lessonId)
    {
        $this->lessonId = $lessonId;

        return $this;
    }

    /**
     * Get lessonId
     *
     * @return integer 
     */
    public function getLessonId()
    {
        return $this->lessonId;
    }

    /**
     * Set studentUserId
     *
     * @param integer $studentUserId
     * @return Lesson_has_Student
     */
    public function setStudentUserId($studentUserId)
    {
        $this->studentUserId = $studentUserId;

        return $this;
    }

    /**
     * Get studentUserId
     *
     * @return integer 
     */
    public function getStudentUserId()
    {
        return $this->studentUserId;
    }

    /**
     * Set absent
     *
     * @param boolean $absent
     * @return Lesson_has_Student
     */
    public function setAbsent($absent)
    {
        $this->absent = $absent;

        return $this;
    }

    /**
     * Get absent
     *
     * @return boolean 
     */
    public function getAbsent()
    {
        return $this->absent;
    }

    /**
     * Set justified
     *
     * @param boolean $justified
     * @return Lesson_has_Student
     */
    public function setJustified($justified)
    {
        $this->justified = $justified;

        return $this;
    }

    /**
     * Get justified
     *
     * @return boolean 
     */
    public function getJustified()
    {
        return $this->justified;
    }
}
