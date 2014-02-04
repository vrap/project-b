<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Speaker_has_lesson
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\Speaker_has_lessonRepository")
 */
class Speaker_has_lesson
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
     * @ORM\Column(name="speaker_user_id", type="integer")
     */
    private $speakerUserId;

    /**
     * @var integer
     *
     * @ORM\Column(name="lesson_id", type="integer")
     */
    private $lessonId;


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
     * Set speakerUserId
     *
     * @param integer $speakerUserId
     * @return Speaker_has_lesson
     */
    public function setSpeakerUserId($speakerUserId)
    {
        $this->speakerUserId = $speakerUserId;

        return $this;
    }

    /**
     * Get speakerUserId
     *
     * @return integer 
     */
    public function getSpeakerUserId()
    {
        return $this->speakerUserId;
    }

    /**
     * Set lessonId
     *
     * @param integer $lessonId
     * @return Speaker_has_lesson
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
}
