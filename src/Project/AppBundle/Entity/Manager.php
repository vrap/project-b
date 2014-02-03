<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Manager
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\ManagerRepository")
 */
class Manager
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
     * @var boolean
     *
     * @ORM\Column(name="isAdministrator", type="boolean")
     */
    private $isAdministrator;

    /**
     * @ORM\OneToOne(targetEntity="Project\AppBundle\Entity\User")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="Project\AppBundle\Entity\Formation", cascade={"persist"})
     */
    private $formation;


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
     * Set isAdministrator
     *
     * @param boolean $isAdministrator
     * @return Manager
     */
    public function setIsAdministrator($isAdministrator)
    {
        $this->isAdministrator = $isAdministrator;

        return $this;
    }

    /**
     * Get isAdministrator
     *
     * @return boolean 
     */
    public function getIsAdministrator()
    {
        return $this->isAdministrator;
    }

    /**
     * Set formation
     *
     * @param \Project\AppBundle\Entity\Formation $formation
     * @return Manager
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
     * Set user
     *
     * @param \Project\AppBundle\Entity\User $user
     * @return Manager
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
}
