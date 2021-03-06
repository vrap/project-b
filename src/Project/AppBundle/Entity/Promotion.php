<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Promotion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\PromotionRepository")
 */
class Promotion
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
     * @var \Date
     *
     * @ORM\Column(name="startDate", type="date")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $startDate;

    /**
     * @var \Date
     *
     * @ORM\Column(name="endDate", type="date")
     * @Assert\Date()
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Formation", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $formation;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Archive", cascade={"persist"})
     */
    private $archive;

    public function __construct() {
        $this->startDate = new \Datetime();
        $this->endDate   = new \Datetime();
    }


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
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Promotion
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
     * @return Promotion
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
     * Set formation
     *
     * @param \Project\AppBundle\Entity\Formation $formation
     * @return Promotion
     */
    public function setFormation(\Project\AppBundle\Entity\Formation $formation = null)
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * Get formation
     *
     * @return \Project\AppBundle\Entity\Formation 
     */
    public function getFormation()
    {
        return $this->formation;
    }

    /**
     * Set archive
     *
     * @param \Project\AppBundle\Entity\Archive $archive
     * @return Promotion
     */
    public function setArchive(\Project\AppBundle\Entity\Archive $archive = null)
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * Get archive
     *
     * @return \Project\AppBundle\Entity\Archive 
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Convert a promotion entity to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFormation()->getName().' ('.$this->getStartDate()->format('Y').' - '.$this->getEndDate()->format('Y').')';
    }
}
