<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Promotion", cascade={"persist"})
     */
    private $promotion;

    /**
     * @ORM\OneToMany(targetEntity="Project\AppBundle\Entity\Lesson", mappedBy="module", cascade={"all"})
     */
    private $lessons;


    public function __construct()
    {
        $this->lessons = new ArrayCollection();
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
     * Set promotion
     *
     * @param \Project\AppBundle\Entity\Promotion $promotion
     * @return Promotion
     */
    public function setPromotion(\Project\AppBundle\Entity\Promotion $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return \Project\AppBundle\Entity\Promotion
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * Retrieve associated lessons.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLessons()
    {
        return $this->lessons;
    }

    /**
     * Convert a module entity to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}