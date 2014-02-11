<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StudentEvaluationCriterion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\StudentEvaluationCriterionRepository")
 */
class StudentEvaluationCriterion
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
     * @var float
     *
     * @ORM\Column(name="score", type="float")
     */
    private $score;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Criterion", cascade={"persist"})
     */
    private $criterion;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\StudentEvaluation", cascade={"persist"})
     */
    private $studentEvaluation;


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
     * Set score
     *
     * @param float $score
     * @return StudentEvaluationCriterion
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return float 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set criterion
     *
     * @param \Project\AppBundle\Entity\Criterion $criterion
     * @return StudentEvaluationCriterion
     */
    public function setCriterion($criterion)
    {
        $this->criterion = $criterion;

        return $this;
    }

    /**
     * Get criterion
     *
     * @return \Project\AppBundle\Entity\Criterion
     */
    public function getCriterion()
    {
        return $this->criterion;
    }

    /**
     * Set studentEvaluation
     *
     * @param \Project\AppBundle\Entity\Evaluation $studentEvaluation
     * @return StudentEvaluationCriterion
     */
    public function setStudentEvaluation($studentEvaluation)
    {
        $this->studentEvaluation = $studentEvaluation;

        return $this;
    }

    /**
     * Get studentEvaluation
     *
     * @return \Project\AppBundle\Entity\Evaluation
     */
    public function getStudentEvaluation()
    {
        return $this->studentEvaluation;
    }
}
