<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Speaker_has_lesson
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\SpeakerLessonRepository")
 */
class SpeakerLesson
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
     * @ORM\OneToOne(targetEntity="Project\AppBundle\Entity\Speaker", cascade={"all"})
     */
    private $speaker;

    /**
     * @ORM\OneToOne(targetEntity="Project\AppBundle\Entity\Lesson", mappedBy="speakerLesson", cascade={"all"})
     * @ORM\JoinColumn(name="lesson_id", referencedColumnName="id")
     */
    private $lesson;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
