<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evaluation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\EvaluationRepository")
 */
class Evaluation
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
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="readed", type="boolean")
     */
    private $readed = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="validated", type="boolean")
     */
    private $validated = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="max", type="smallint")
     */
    private $max;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Module", cascade={"persist"})
     */
    private $module;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Speaker", cascade={"persist"})
     */
    private $speaker;

    /**
     * @ORM\OneToMany(targetEntity="Project\AppBundle\Entity\StudentEvaluation", mappedBy="evaluation", cascade={"persist"})
     */
    private $studentEvaluations;


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
     * Set description
     *
     * @param string $description
     * @return Evaluation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set readed
     *
     * @param boolean $readed
     * @return Evaluation
     */
    public function setReaded($readed)
    {
        $this->readed = $readed;

        return $this;
    }

    /**
     * Get readed
     *
     * @return boolean 
     */
    public function getReaded()
    {
        return $this->readed;
    }

    /**
     * Set validated
     *
     * @param boolean $validated
     * @return Evaluation
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Get validated
     *
     * @return boolean 
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * Set max
     *
     * @param integer $max
     * @return Evaluation
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return integer 
     */
    public function getMax()
    {
        return $this->max;
    }


    /**
     * Set module_id
     *
     * @param \Project\AppBundle\Entity\Module $moduleId
     * @return Evaluation
     */
    public function setModule(\Project\AppBundle\Entity\Module $moduleId = null)
    {
        $this->module = $moduleId;

        return $this;
    }

    /**
     * Get module_id
     *
     * @return \Project\AppBundle\Entity\Module 
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set speaker
     *
     * @param \Project\AppBundle\Entity\Speaker $speakerId
     * @return Evaluation
     */
    public function setSpeaker(\Project\AppBundle\Entity\Speaker $speakerId = null)
    {
        $this->speaker = $speakerId;

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
     * Get student evaluations
     */
    public function getStudentEvaluations()
    {
        return $this->studentEvaluations;
    }

    /**
     * Set student evaluations
     */
    public function setStudentEvaluations(ArrayCollection $studentEvaluations)
    {
        $this->studentEvaluations = $studentEvaluations;
    }
}
