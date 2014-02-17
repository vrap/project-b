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
     * @ORM\Column(name="is_administrator", type="boolean")
     */
    private $is_administrator;

    /**
     * @ORM\OneToOne(targetEntity="Project\AppBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Formation", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
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
     * Set is_administrator
     *
     * @param boolean $isAdministrator
     * @return Manager
     */
    public function setIsAdministrator($isAdministrator)
    {
        $this->is_administrator = $isAdministrator;

        return $this;
    }

    /**
     * Get is_administrator
     *
     * @return boolean 
     */
    public function getIsAdministrator()
    {
        return $this->is_administrator;
    }

    /**
     * Set user
     *
     * @param \Project\AppBundle\Entity\User $user
     * @return Manager
     */
    public function setUser(\Project\AppBundle\Entity\User $user)
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
     * Set formation
     *
     * @param \Project\AppBundle\Entity\Formation $formation
     * @return Manager
     */
    public function setFormation(\Project\AppBundle\Entity\Formation $formation)
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
}
