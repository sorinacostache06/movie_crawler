<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rottentomatoes
 *
 * @ORM\Table(name="rottentomatoes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RottentomatoesRepository")
 */
class Rottentomatoes
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, unique=true)
     */
    private $link;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float", nullable=true)
     */
    private $rating;

    /**
     * @var bool
     *
     * @ORM\Column(name="was_matched", type="boolean", nullable=true)
     */
    private $wasMatched;

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
     * Set title
     *
     * @param string $title
     *
     * @return Rottentomatoes
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return Rottentomatoes
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set rating
     *
     * @param float $rating
     *
     * @return Rottentomatoes
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return bool
     */
    public function isWasMatched()
    {
        return $this->wasMatched;
    }

    /**
     * @param bool $wasMatched
     */
    public function setWasMatched($wasMatched)
    {
        $this->wasMatched = $wasMatched;
    }
}

