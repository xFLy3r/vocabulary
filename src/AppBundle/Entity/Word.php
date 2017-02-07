<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Word
 *
 * @ORM\Table(name="word")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WordRepository")
 */
class Word
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
     * @ORM\Column(type="string", length=255)
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Word", mappedBy="name")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Language")
     */
    private $language;

    /**
     * @var
     *
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Word")
     */
    private $translations;

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
     * Set name
     *
     * @param string $name
     *
     * @return Word
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return Word
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * Add translation
     *
     * @param \AppBundle\Entity\Word $translation
     *
     * @return Word
     */
    public function addTranslation(\AppBundle\Entity\Word $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param \AppBundle\Entity\Word $translation
     */
    public function removeTranslation(\AppBundle\Entity\Word $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}
