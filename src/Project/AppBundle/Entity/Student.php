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
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="corporation_id", type="integer")
     */
    private $corporationId;

    /**
     * @var integer
     *
     * @ORM\Column(name="promotion_id", type="integer")
     */
    private $promotionId;


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
     * Set userId
     *
     * @param integer $userId
     * @return Student
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set corporationId
     *
     * @param integer $corporationId
     * @return Student
     */
    public function setCorporationId($corporationId)
    {
        $this->corporationId = $corporationId;

        return $this;
    }

    /**
     * Get corporationId
     *
     * @return integer 
     */
    public function getCorporationId()
    {
        return $this->corporationId;
    }

    /**
     * Set promotionId
     *
     * @param integer $promotionId
     * @return Student
     */
    public function setPromotionId($promotionId)
    {
        $this->promotionId = $promotionId;

        return $this;
    }

    /**
     * Get promotionId
     *
     * @return integer 
     */
    public function getPromotionId()
    {
        return $this->promotionId;
    }
}
