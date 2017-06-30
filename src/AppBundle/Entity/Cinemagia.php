<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cinemagia
 *
 * @ORM\Table(name="cinemagia")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CinemagiaRepository")
 */
class Cinemagia
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
     * @ORM\Column(name="title", type="string", length=255, unique=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=255, nullable=true)
     */
    private $year;

    /**
     * @var array
     *
     * @ORM\Column(name="actors", type="array", nullable=true)
     */
    private $actors;

    /**
     * @var array
     *
     * @ORM\Column(name="directors", type="array", nullable=true)
     */
    private $directors;

    /**
     * @var array
     *
     * @ORM\Column(name="genre", type="array", nullable=true)
     */
    private $genre;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float")
     */
    private $rating;

    /**
     * @var float
     *
     * @ORM\Column(name="ratingImdb", type="float")
     */
    private $ratingImdb;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

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
     * @return Cinemagia
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
     * @return Cinemagia
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
     * @param string $year
     *
     * @return Cinemagia
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string
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
     * @return Cinemagia
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
     * @return Cinemagia
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
     * @return Cinemagia
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
     * @param float $rating
     *
     * @return Cinemagia
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
     * Set ratingImdb
     *
     * @param float $ratingImdb
     *
     * @return Cinemagia
     */
    public function setRatingImdb($ratingImdb)
    {
        $this->ratingImdb = $ratingImdb;

        return $this;
    }

    /**
     * Get ratingImdb
     *
     * @return float
     */
    public function getRatingImdb()
    {
        return $this->ratingImdb;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Cinemagia
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
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

