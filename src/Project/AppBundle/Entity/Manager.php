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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
