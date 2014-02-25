<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lesson
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\LessonRepository")
 */
class Lesson
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime")
     */
    private $endDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="timecard", type="boolean")
     */
    private $timecard;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Module", inversedBy="lessons", cascade={"all"})
     */
    private $module;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Speaker", inversedBy="lessons", cascade={"all"})
     */
    private $speaker;


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
     * Set name
     *
     * @param string $name
     * @return Lesson
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Lesson
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Lesson
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set timecard
     *
     * @param boolean $timecard
     * @return Lesson
     */
    public function setTimecard($timecard)
    {
        $this->timecard = $timecard;

        return $this;
    }

    /**
     * Get timecard
     *
     * @return boolean 
     */
    public function getTimecard()
    {
        return $this->timecard;
    }

    /**
     * Set speaker
     *
     * @param \Project\AppBundle\Entity\Speaker $speaker
     * @return Speaker
     */
    public function setSpeaker(\Project\AppBundle\Entity\Speaker $speaker = null)
    {
        $this->speaker = $speaker;

        return $this;
    }

    /**
     * Get speaker
     *
     * @return \Project\AppBundle\Entity\Speaker 
     */
    public function getSpeaker()
    {
        return $this->speaker;
    }
    
    /**
     * Get module
     * 
     * @return \Project\AppBundle\Entity\Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set Module
     * 
     * @param \Project\AppBundle\Entity\Module $module
     */
    public function setModule(\Project\AppBundle\Entity\Module $module = null)
    {
        $this->module = $module;
        
        return $this;
    }


}