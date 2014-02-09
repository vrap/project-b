<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Criterion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\CriterionRepository")
 */
class Criterion
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
     * @ORM\Column(name="max", type="smallint")
     */
    private $max;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Evaluation", cascade={"persist"})
     */
    private $evaluation;


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
     * Set max
     *
     * @param integer $max
     * @return Criterion
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
     * Set evaluation
     *
     * @param \stdClass $evaluation
     * @return Criterion
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    /**
     * Get evaluation
     *
     * @return \stdClass 
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }
}
