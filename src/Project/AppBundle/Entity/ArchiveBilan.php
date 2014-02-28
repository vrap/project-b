<?php

namespace Project\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArchiveBilan
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Project\AppBundle\Entity\ArchiveBilanRepository")
 */
class ArchiveBilan
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
     * @ORM\Column(name="type", type="string", length=45)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="blob")
     */
    private $content;

    /**
     * @ORM\OneToOne(targetEntity="Project\AppBundle\Entity\Promotion", cascade={"persist"})
     */
    private $promotion;

    /**
     * @ORM\ManyToOne(targetEntity="Project\AppBundle\Entity\Archive", inversedBy="archiveBilans")
     */
    private $archive;


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
     * Set type
     *
     * @param string $type
     * @return ArchiveBilan
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return ArchiveBilan
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set promotion
     *
     * @param \Project\AppBundle\Entity\Promotion $promotion
     * @return ArchiveBilan
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
}
