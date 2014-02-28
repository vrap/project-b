<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Student
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\StudentRepository")
 */
class Student
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
     * @ORM\OneToOne(targetEntity="Project\AppBundle\Entity\User", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Corporation", cascade={"persist"})
     */
    private $corporation;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Promotion", cascade={"persist"})
     */
    private $promotion;


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
     * Set user
     *
     * @param \Project\AppBundle\Entity\User $user
     * @return Student
     */
    public function setUser(\Project\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Project\AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set corporation
     *
     * @param \Project\AppBundle\Entity\Corporation $corporation
     * @return Student
     */
    public function setCorporation(Corporation $corporation = null)
    {
        $this->corporation = $corporation;

        return $this;
    }

    /**
     * Get corporation
     *
     * @return \Project\AppBundle\Entity\Corporation 
     */
    public function getCorporation()
    {
        return $this->corporation;
    }

    /**
     * Set promotion
     *
     * @param \Project\AppBundle\Entity\Promotion $promotion
     * @return Student
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
}
