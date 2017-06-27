<?php
/**
 * Created by Sorina Costache.
 * User: sorina
 * Date: 18.04.2017*/

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table(name="site")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SiteRepository")
 */
class Site
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime")
     */
    private $dateAdded;

    /**
     *  @ORM\OneToMany(targetEntity="Link", mappedBy="site")
     */
    private $link;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->movies = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add link
     *
     * @param \AppBundle\Entity\Link $link
     *
     * @return
     */
    public function addLink(\AppBundle\Entity\Link $link)
    {
        $this->link[] = $link;
        return $this;
    }
    /**
     * Remove link
     *
     * @param \AppBundle\Entity\Link $link
     */
    public function removeLink(\AppBundle\Entity\Link $link)
    {
        $this->link->removeElement($link);
    }
    /**
     * Get link
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return Site
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}

