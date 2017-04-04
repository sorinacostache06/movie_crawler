<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Movie
 *
 * @ORM\Table(name="movie")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MovieRepository")
 * @UniqueEntity("link")
 */
class Movie
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
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="year", type="date")
     */
    private $year;

    /**
     * @var array
     *
     * @ORM\Column(name="actors", type="array")
     */
    private $actors;

    /**
     * @var array
     *
     * @ORM\Column(name="directors", type="array")
     */
    private $directors;

    /**
     * @var array
     *
     * @ORM\Column(name="genre", type="array")
     */
    private $genre;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;


    /**
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="movies")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;

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
     * @return Movie
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
     * @return Movie
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
     * Set year
     *
     * @param \DateTime $year
     *
     * @return Movie
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return \DateTime
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set actors
     *
     * @param array $actors
     *
     * @return Movie
     */
    public function setActors($actors)
    {
        $this->actors = $actors;

        return $this;
    }

    /**
     * Get actors
     *
     * @return array
     */
    public function getActors()
    {
        return $this->actors;
    }

    /**
     * Set directors
     *
     * @param array $directors
     *
     * @return Movie
     */
    public function setDirectors($directors)
    {
        $this->directors = $directors;

        return $this;
    }

    /**
     * Get directors
     *
     * @return array
     */
    public function getDirectors()
    {
        return $this->directors;
    }

    /**
     * Set genre
     *
     * @param array $genre
     *
     * @return Movie
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return array
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return Movie
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set site
     *
     * @param \AppBundle\Entity\Site $site
     * @return $this
     */
    public function setSite(\AppBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \AppBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }
}

