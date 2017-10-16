<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Joueur
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JoueurRepository")
 */
class Joueur extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Partie", mappedBy="joueur1")
     */
    private $partiesj1;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Partie", mappedBy="joueur2")
     */
    private $partiesj2;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->partiesj1 = new \Doctrine\Common\Collections\ArrayCollection();
        $this->partiesj2 = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add partiesj1
     *
     * @param \AppBundle\Entity\Partie $partiesj1
     *
     * @return Joueur
     */
    public function addPartiesj1(\AppBundle\Entity\Partie $partiesj1)
    {
        $this->partiesj1[] = $partiesj1;

        return $this;
    }

    /**
     * Remove partiesj1
     *
     * @param \AppBundle\Entity\Partie $partiesj1
     */
    public function removePartiesj1(\AppBundle\Entity\Partie $partiesj1)
    {
        $this->partiesj1->removeElement($partiesj1);
    }

    /**
     * Get partiesj1
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPartiesj1()
    {
        return $this->partiesj1;
    }

    /**
     * Add partiesj2
     *
     * @param \AppBundle\Entity\Partie $partiesj2
     *
     * @return Joueur
     */
    public function addPartiesj2(\AppBundle\Entity\Partie $partiesj2)
    {
        $this->partiesj2[] = $partiesj2;

        return $this;
    }

    /**
     * Remove partiesj2
     *
     * @param \AppBundle\Entity\Partie $partiesj2
     */
    public function removePartiesj2(\AppBundle\Entity\Partie $partiesj2)
    {
        $this->partiesj2->removeElement($partiesj2);
    }

    /**
     * Get partiesj2
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPartiesj2()
    {
        return $this->partiesj2;
    }
}
