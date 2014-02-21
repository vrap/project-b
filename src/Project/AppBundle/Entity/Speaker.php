<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Speaker
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\SpeakerRepository")
 */
class Speaker
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
     * @return Speaker
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
