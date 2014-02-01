<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Module
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\ModuleRepository")
 */
class Module
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
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Formation", cascade={"persist"})
     */
    private $formation;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Speaker", cascade={"persist"})
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
     * @return Module
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
     * Set formation
     *
     * @param \Project\AppBundle\Entity\Formation $formation
     * @return Module
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
     * Set speaker
     *
     * @param \Project\AppBundle\Entity\Speaker $speaker
     * @return Module
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
}
